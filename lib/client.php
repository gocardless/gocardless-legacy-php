<?php

class Client {
	
	public static $redirect_uri;
	
	public static $base_url = 'https://sandbox.gocardless.com';
	public static $base_urls = array(
		'production'	=> 'https://gocardless.com',
		'sandbox'		=> 'https://sandbox.gocardless.com'
	);
	
	public static $response_format;
	
	public static $api_path = '/api/v1';
	
	public static function init() {
		
		//if (!isset(GoCardless::$account_details['app_id'])) {
		//	throw new GoCardlessClientException('No app_id specfied');
		//}
		
		//echo '<pre>';
		//var_dump(get_class_vars('GoCardless'));
		//echo '</pre>';
		
	}
	
	public static function init_old() {
		
		//foreach ($config as $key => $value) {
		//	//Client::${$key} = $value;
		//}
		//
		//if (!isset($config['app_id'])) {
		//	throw new GoCardlessClientException('No app_id specfied');
		//}
		//
		//if (!isset($config['app_secret'])) {
		//	throw new GoCardlessClientException('No app_secret specfied');
		//}
		//
		//// If environment is not set then default to production
		//if (!isset(Client::$environment)) {
		//	Client::$environment = 'production';
		//}
		//
		//// If response_format is not set then default to json
		//if (!isset(Client::$response_format)) {
		//	Client::$response_format = 'application/json';
		//}
		//
		// If base_url is not set then set it based on environment

		//if (!isset(Client::$base_url)) {
		//	Client::$base_url = Client::$base_urls[GoCardless::$environment];
		//}
		
		//if (GoCardless::$environment == 'production') {
		//	echo ' tis prod!';
		//} elseif (GoCardless::$environment == 'sandbox') {
		//	echo ' tis sand!';
		//}
		
		//echo "<pre>Vars: ";
		//var_dump(get_class_vars('Client'));
		//echo "\nMethods: ";
		//var_dump(get_class_methods('Client'));
		//echo '</pre>';
		
	}
	
	// authorize_url
	// fetch_access_token
	// access_token
	// access_token
	
	/**
	 * Configure a GET request
	 *
	 * @param string $url The URL to make the request to
	 * @param array $params The parameters to use for the POST body
	 *
	 * @return string The response text
	 */
	protected function api_get($path, $params = array()) {
		return Client::request('get', $path, $params);
	}
	
	/**
	 * Configure a POST request
	 *
	 * @param string $url The URL to make the request to
	 * @param array $params The parameters to use for the POST body
	 *
	 * @return string The response text
	 */
	protected function api_post($path, $data = array()) {
		return Client::request('post', $path, $data);
	}

	// api_put
	// Merchant
	// Subscription
	// pre_authorization
	// user
	// bill	
	// payment
	// Create bill
	
	/**
	 * Generate a URL to give a user to create a new subscription
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function new_subscription_url($params) {
		return Client::new_limit_url('subscription', $params);
	}
	
	/**
	 * Generate a URL to give a user to create a new pre-authorized payment
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public function new_pre_authorization_url($params) {
		return Client::new_limit_url('pre_authorization', $params);
	}
	
	/**
	 * Generate a URL to give a user to create a new bill
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public function new_bill_url($params) {
		return Client::new_limit_url('bill', $params);
	}
	
	/**
	 * Send an HTTP request to confirm the creation of a new payment resource
	 *
	 * @param array $params Parameters to send with the request
	 *
	 * @return string The result of the HTTP request
	 */
	public function confirm_resource($params) {
		
		$required_params = array(
			'resource_id', 'resource_type'
		);
		
		foreach ($required_params as $key => $value) {
			if (!isset($params[$value])) {
				throw new GoCardlessArgumentsException("$value missing");
			}
		}
		
		// Build url
		$url = Client::$base_url . Client::$api_path . '/confirm';
		
		// Prep curl for http basic auth
		$params['curl_opts'][CURLOPT_USERPWD] = GoCardless::$account_details['app_id'] . ':' . GoCardless::$account_details['app_secret'];
		
		// If no method-specific redirect submitted, use class level if available
		if (!isset($params['redirect_uri']) && isset(Client::$redirect_uri)) {
			$params['redirect_uri'] = Client::$redirect_uri;
		}
		
		// Do query
		$confirm = Client::api_post($url, $params);
		
		// Return the result
		return $confirm;
		
	}
	
	/**
	 * Test whether a webhook is valid or not
	 *
	 * @param array params The contents of the webhook in array form
	 *
	 * @return boolean If valid returns true
	 */
	public function validate_webhook($params) {
		
		$sig = $params['payload']['signature'];
		unset($params['payload']['signature']);
		
		if (!isset($sig)) {
			return false;
		}
		
		$data = array(
			'data'		=> $params['payload'],
			'secret'	=> GoCardless::$account_details['app_secret'],
			'signature'	=> $sig
		);
		
		return Client::validate_signature($data);
		
	}
	
	/**
	 * Makes an HTTP request
	 *
	 * @param string $url The URL to make the request to
	 * @param array $params The parameters to use for the POST body
	 *
	 * @return string The response text
	 */
	protected function request($method, $path, $opts = array()) {
		
		$ch = curl_init($path);
		
		$curl_options = array(
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 60
		);
		
		// $params['curl_opts'][CURLOPT_USERPWD]
		
		if (is_array($opts['curl_opts'])) {
			foreach ($opts['curl_opts'] as $key => $value) {
				$curl_options[$key] = $value;
			}
			unset($opts['curl_opts']);
		}
		
		if ($method == 'post') {

			$curl_options[CURLOPT_POST] = 1;

			if ($opts) {
				$curl_options[CURLOPT_POSTFIELDS] = http_build_query($opts, null, '&');
			}
			
		} elseif ($method == 'get') {
			
			
			
		}
		
		// Debug
		if ($method == 'post') {
			// POST request, so show url and vars
			$vars = htmlspecialchars(print_r($curl_options[CURLOPT_POSTFIELDS], true));
			echo '<pre>';
			echo "POST: $path\n";
			echo "Post vars sent:\n";
			echo "$vars\n";
			echo "Full curl vars:\n";
			print_r($curl_options);
			echo '</pre>';
		} elseif ($method == 'get') {
			// GET request, so show just show url
			echo '<pre>';
			echo "GET: $path\n";
			echo '</pre>';
		} else {
			echo "Method not set!";
		}
		
		curl_setopt_array($ch, $curl_options);
		
		$result = curl_exec($ch);
		
		// Debug
		echo "<pre>\n\nResult\n\nUrl: $path\nVars: ";
		print_r(curl_getinfo($ch));
		echo "</pre>";
		
		// Grab the response code and throw an exception if it's not 200
		$http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_response_code != 200) {
			$response = json_decode($result, true);
			throw new GoCardlessApiException($response['error'], $http_response_code);
		}
		
		curl_close($ch);
		
		return $result;
		
	}
	
	// Sign params
	
	/**
	 * Confirm whether a signature is valid
	 *
	 * @return string A URL-encoded string of parameters
	 */
	function validate_signature($params) {
		
		$new_sig = Utils::generate_signature($params['data'], $params['secret']);
		
		if ($new_sig == $params['signature']) {
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Generates a nonce
	 *
	 * @return string Base64 encoded nonce
	 */
	public static function generate_nonce() {
		
		$n = 1;
		$rand = '';
		
		do {
			$rand .= rand(1, 256);
			$n++;
		} while ($n <= 45);
		
		return base64_encode($rand);
		
	}
	
	/**
	 * Generate a new payment url
	 *
	 * @param string $resource Payment type
	 * @param string $params The specific parameters for this payment
	 *
	 * @return string The new payment URL
	 */
	private static function new_limit_url($type, $limit_params) {
		
		// If no method-specific redirect submitted then
		// use class level if available
		if (!isset($limit_params['redirect_uri']) && isset(Client::$redirect_uri)) {
			$limit_params['redirect_uri'] = Client::$redirect_uri;
		}
		
		// Add in merchant id
		$limit_params['merchant_id'] = GoCardless::$account_details['merchant_id'];
		
		// Add passed params to an array named by type
		$limit_params = array($type => $limit_params);
		
		// Merge passed and mandatory params
		$request = array_merge($limit_params, Utils::generate_mandatory_params());
		
		// Generate signature
		$request['signature'] = Utils::generate_signature($request, GoCardless::$account_details['app_secret']);

		// Generate query string from all parameters
		$query_string = Utils::generate_query_string($request);
		
		// Generate url NB. Pluralises resource
		$url = Client::$base_url . '/connect/' . $type . 's/new?' . $query_string;
		
		// Return the result
		return $url;
		
	}

}

Client::init();

?>