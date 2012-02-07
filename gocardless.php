<?php

class GoCardless {
	
	public $app_identifier;
	public $app_secret;
	public $access_token;
	public $redirect_uri;
	
	public $environment;
	public $base_url;
	public $base_urls = array(	'production'	=> 'https://gocardless.com',
								'sandbox'		=> 'https://sandbox.gocardless.com'
								);
	
	public $api_path = '/api/v1';
	
	public static $curl_options = array(	CURLOPT_CONNECTTIMEOUT	=> 10,
											CURLOPT_RETURNTRANSFER	=> true,
											CURLOPT_TIMEOUT			=> 60
											);
	
	/**
	 * Constructor, adds intialization config vars to class scope
	 *
	 * @param array $config GoCardless API keys
	 */
	function __construct($config) {
		
		if (!isset($config)) {
			// Fail, no config vars
			
		}
		
		foreach ($config as $key => $value) {
			$this->{$key} = $value;
		}
		
		// If environment is not set then default to production
		if (!isset($this->environment)) {
			$this->environment = 'production';
		}
		
		// Set base_url based on environment
		$this->base_url = $this->base_urls[$this->environment];
		
		include_once 'lib/merchant.php';
		$this->merchant = new Merchant($this);
		
	}

/*	
	// Ditched these functions as consolidated them into generate_url but realised that the ruby lib uses them
	// To revive?
	
	public function new_subscription_url($params) {
		
		return $this->generate_url('connect/pre_authorizations/new', 'pre_authorization', $params);
		
	}
	
	public function new_pre_authorization_url($params) {
		
		return $this->generate_url('connect/pre_authorizations/new', 'pre_authorization', $params);
		
	}
	
	public function new_bill_url($params) {
		
		return $this->generate_url('connect/bills/new', 'bill', $params);
		
	}
*/
	
	/**
	 * Generate a new payment url
	 *
	 * @param string $resource Payment type
	 * @param string $params The specific parameters for this payment
	 *
	 * @return string The new payment URL
	 */
	public function generate_url($resource_type, $params) {
		
		// If no method-specific redirect submitted, use class level if available
		if (!isset($params['redirect_uri']) && $this->redirect_uri) {
			$params['redirect_uri'] = $this->redirect_uri;
		}
		
		// Add passed params to an array called bill
		$params = array($resource_type => $params);
		
		// Merge passed and mandatory params
		$request = array_merge($params, $this->generate_mandatory_params());
		
		// Generate signature
		$request['signature'] = $this->generate_signature($request, $this->app_secret);
		
		// Generate query string from all parameters
		$query_string = $this->generate_query_string($request);
		
		// Generate url NB. Pluralises resource
		$url = $this->base_url . '/connect/' . $resource_type . 's/new?' . $query_string;
		
		// Return the result
		return $url;
		
	}
	
	/**
	 * Send an HTTP request to confirm the creation of a new payment resource
	 *
	 * @param string $resource_id Unique id of the created resource
	 * @param string $resource_type Type of payment
	 *
	 * @return string The result of the HTTP request
	 */
	public function confirm_resource($resource_id, $resource_type) {
		
		// Build url
		$url = $this->base_url . $this->api_path . '/confirm';
		
		// Prep curl for http basic auth
		self::$curl_options[CURLOPT_USERPWD] = $this->app_identifier . ':' . $this->app_secret;
		
		$params = array(	'resource_id'	=> $resource_id,
							'resource_type'	=> $resource_type
							);
		
		// If no method-specific redirect submitted, use class level if available
		if (!isset($params['redirect_uri']) && $this->redirect_uri) {
			$params['redirect_uri'] = $this->redirect_uri;
		}
		
		// Do query
		$confirm = $this->makeRequest($url, $params);
		
		// Return the result
		return $confirm;
		
	}
	
	// API objects
	//
	// Merchant
	// Bill
	// PreAuthorization
	// Subscription
	// User
	// Payment
	
	public function merchants($id) {
		
		return $this->fetch_resource('merchants', $id);
		
	}
	
	
	// HELPERS
	
	/**
	 * Generate mandatory payment parameters: client_id, nonce and timestamp
	 *
	 * @return array Mandatory payment parameters
	 */
	private function generate_mandatory_params() {
		
		$request = array(	'client_id'	=> $this->app_identifier,
							'nonce'		=> $this->generate_nonce(),
							'timestamp'	=> date('Y-m-d\TH:i:s\Z')
							);
		
		return $request;
		
	}
	
	/**
	 * Magical function to generate, encode, re-order variables for the query string
	 *
	 *
	 * @return string A URL-encoded string of parameters
	 */
	private function generate_query_string($params, &$pairs = array(), $namespace = null) {
		
		if (is_array($params)) {
			
			foreach ($params as $k => $v) {
				
				if (is_int($k)) {
					$this->generate_query_string($v, $pairs, $namespace . '[]');
				} else {
					$this->generate_query_string($v, $pairs, $namespace !== null ? $namespace . "[$k]" : $k);
				}
				
			}
			
			if ($namespace !== null) {
				return $pairs;
			}
	
			if (empty($pairs)) {
				return '';
			}
			
			sort($pairs);
			$strs = array_map('implode', array_fill(0, count($pairs), '='), $pairs);
			
			return implode('&', $strs);
			
		} else {
			
			$pairs[] = array(rawurlencode($namespace), rawurlencode($params));
			
		}
		
	}
	
	/**
	 * Generate a signature for a request given the app secret
	 *
	 * @return string A URL-encoded string of parameters
	 */
	function generate_signature($data, $secret) {
		
		return hash_hmac('sha256', $this->generate_query_string($data), $secret);
		
	}
	
	/**
	 * Generates a nonce
	 *
	 * @return string Base64 encoded nonce
	 */
	function generate_nonce() {
		
		$n = 1;
		$rand = '';
		
		do {
			$rand .= rand(1, 256);
			$n++;
		} while ($n <= 45);
		
		return base64_encode($rand);
		
	}
	
	function fetch_resource($object, $id) {
		
		// Build URL
		$url = $this->base_url . $this->api_path . '/' . $object . '/' . $id;
		
		echo '<pre>';
		print_r($this->base_url);
		echo '</pre>';
		
		// Add Authorization header
		self::$curl_options[CURLOPT_HTTPHEADER] = array('Authorization: Bearer ' . $this->access_token);
		
		echo '<pre>';
		print_r(self::$curl_options);
		echo '</pre>';
		
		// Do query
		return $this->makeRequest($url);
		
	}
	
	/**
	 * Makes an HTTP request.
	 *
	 * @param string $url The URL to make the request to
	 * @param array $params The parameters to use for the POST body
	 * @param CurlHandler $ch Initialized curl handle
	 *
	 * @return string The response text
	 */
	protected function makeRequest($url, $params = null, $ch = null) {
		
		if (!isset($ch)) {
			$ch = curl_init();
		}
		
		$opts = self::$curl_options;
		
		if ($params) {
			$opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
		}
		
		$opts[CURLOPT_URL] = $url;
		
		// disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
		// for 2 seconds if the server does not support this header.
		if (isset($opts[CURLOPT_HTTPHEADER])) {
			$existing_headers = $opts[CURLOPT_HTTPHEADER];
			$existing_headers[] = 'Expect:';
			$opts[CURLOPT_HTTPHEADER] = $existing_headers;
		} else {
			$opts[CURLOPT_HTTPHEADER] = array('Expect:');
		}
		
		curl_setopt_array($ch, $opts);
		$result = curl_exec($ch);
		
		//$http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		//if ($http_response_code != 200) {
		//	var_dump($http_response_code);
		//}
		
		curl_close($ch);
		
		return $result;
		
	}
	
}