<?php

class GoCardless {
	
	protected $environment;
	protected $client_id;
	protected $app_identifier;
	protected $app_secret;
	protected $access_token;
	
	protected $base_url = 'https://sandbox.gocardless.com/';
	
	public static $curl_options = array(	CURLOPT_CONNECTTIMEOUT	=> 10,
											CURLOPT_RETURNTRANSFER	=> true,
											CURLOPT_TIMEOUT			=> 60
											);
	
	/*
	* Constructor
	* 
	* 
	*/
	function __construct($config) {
		
		foreach ($config as $key => $value) {
			$this->{$key} = $value;
		}
		
	}
	
	// METHODS

	public function new_subscription_url($params) {
		
		return $this->generate_url('connect/subscriptions/new', 'subscription', $params);
		
	}
	
	public function new_pre_authorization_url($params) {
		
		return $this->generate_url('connect/pre_authorizations/new', 'pre_authorization', $params);
		
	}
	
	public function new_bill_url($params) {
		
		return $this->generate_url('connect/bills/new', 'bill', $params);

	}
	
	private function generate_url($endpoint, $param_wrapper, $params) {
		
		// Add passed params to an array called bill
		$params = array($param_wrapper => $params);
		
		// Merge passed and mandatory params
		$request = array_merge($params, $this->generate_mandatory_params());
		
		// Generate signature
		$request['signature'] = $this->generate_signature($request, $this->app_secret);
		
		// Generate query string from all parameters
		$query_string = $this->generate_query_string($request);
		
		// Base url + endpoint + query string
		$url = $this->base_url . $endpoint . '?' . $query_string;
		
		// Encoded for html
		$url = htmlspecialchars($url);
		
		// Return the result
		return $url;
		
	}
	
	// HELPERS
	
	private function generate_mandatory_params() {
		
		// Mandatory parameters
		$request = array(	'client_id'	=> $this->client_id,
							'nonce'		=> $this->generate_nonce(),
							'timestamp'	=> date('Y-m-d\TH:i:s\Z')
							);
		
		return $request;
		
	}
	
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
			$strs = array_map('implode', array_fill(0, count($pairs), "="), $pairs);
			
			return implode('&', $strs);
			
		} else {
			
			$pairs[] = array(rawurlencode($namespace), rawurlencode($params));
			
		}
		
	}
	
	
	function generate_signature($data, $secret) {
		
		return hash_hmac("sha256", $this->generate_query_string($data), $secret);
		
	}

	function generate_nonce() {

		$n = 1;

		do {
			$rand .= rand(1, 256);
			$n++;
		} while ($n <= 45);
		
		return base64_encode($rand);
		
	}
	

	/**
	 * Makes an HTTP request. This method can be overridden by subclasses if
	 * developers want to do fancier things or use something other than curl to
	 * make the request.
	 *
	 * @param string $url The URL to make the request to
	 * @param array $params The parameters to use for the POST body
	 * @param CurlHandler $ch Initialized curl handle
	 *
	 * @return string The response text
	 */
	protected function makeRequest($url, $params, $ch = null) {
		
		if (!$ch) {
			$ch = curl_init();
		}
	
		$opts = self::$CURL_OPTS;
		
		if ($this->useFileUploadSupport()) {
			$opts[CURLOPT_POSTFIELDS] = $params;
		} else {
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
	
		if (curl_errno($ch) == 60) { // CURLE_SSL_CACERT
			
			self::errorLog('Invalid or no certificate authority found, ' . 'using bundled information');
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/fb_ca_chain_bundle.crt');
			$result = curl_exec($ch);
		}
	
		if ($result === false) {
			
			$e = new FacebookApiException(array(
				'error_code' => curl_errno($ch),
				'error' => array(
				'message' => curl_error($ch),
				'type' => 'CurlException',
				),
			));
			
			curl_close($ch);
			
			throw $e;
			
		}
		
		curl_close($ch);
		return $result;
	}
	
}