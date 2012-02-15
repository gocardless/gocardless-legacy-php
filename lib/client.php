<?php

/**
 * GoCardless client functions
 *
 */

/**
 * GoCardless client class
 *
 */
class GoCardless_Client {

	/** @type array Account details for this instance of GoCardless_Client */
	public $account_details;
	
	/** @type string The (empty) base_url to use for API queries */
	public static $base_url;
	
	/** @type array Array of possible base_urls to use */
	public static $base_urls = array(
		'production'	=> 'https://gocardless.com',
		'sandbox'		=> 'https://sandbox.gocardless.com'
	);
	
	/** @type string The path to use to call the API */
	public static $api_path = '/api/v1';
	
	/** @type string The url to redirect the user to */
	public $redirect_uri;
	
	/**
	 * Constructor, creates a new instance of GoCardless_Client
	 *
	 * @param array $account_details Parameters 
	 */
	public function __construct($account_details) {
		
		// Fetch account_details
		foreach ($account_details as $key => $value) {
			$this->account_details[$key] = $value;
		}
		
		// Check for app_id
		if (!isset($this->account_details['app_id'])) {
			throw new GoCardlessClientException('No app_id specified');
		}
		
		// Check for app_secret
		if (!isset($this->account_details['app_secret'])) {
			throw new GoCardlessClientException('No app_secret specfied');
		}
		
		// If environment is not set then default to production
		if (!isset(GoCardless::$environment)) {
			GoCardless::$environment = 'production';
		}
		
		// If base_url is not set then set it based on environment
		if (!isset(GoCardless_Client::$base_url)) {
			GoCardless_Client::$base_url = GoCardless_Client::$base_urls[GoCardless::$environment];
		}
		
	}
	
	/**
	 * Generate the OAuth authorize url
	 *
	 * @param array $options The parameters to use
	 *
	 * @return string The generated url
	 */
	public function authorizeUrl($options) {
		
		if (!isset($options['redirect_uri'])) {
			throw new GoCardlessArgumentsException('redirect_uri required');
		}
		
		$params = array(
			'client_id'		=> GoCardless::$account_details['app_id'],
			'redirect_uri'	=> $options['redirect_uri'],
			'response_type'	=> 'code',
			'scope'			=> 'manage_merchant'
		);
		
		return OAuth::authorizeUrl($params);
		
	}
	
	/**
	 * Fetch an access token for the current user
	 *
	 * @param string $auth_code The authorization code
	 * @param array $options The parameters to use
	 *
	 * @return string The access token
	 */
	public function fetchAccessToken($options){
		
		if (!isset($options['redirect_uri'])) {
			throw new GoCardlessArgumentsException('redirect_uri required');
		}
		
		$response = OAuth::getToken($options);
		$token_response = json_decode($response, true);
		$merchant = explode(':', $token_response['scope']);
		$merchant_id = $merchant[1];
		$access_token = $token_response['access_token'];
		
		$return = array(
			'merchant_id'	=> $merchant_id,
			'access_token'	=> $access_token
		);
		
		return $return;
		
	}
	
	/**
	 * Configure a GET request
	 *
	 * @param string $path The URL to make the request to
	 * @param array $params The parameters to use for the POST body
	 *
	 * @return string The response text
	 */
	public static function apiGet($path, $params = array()) {
		return GoCardless_Client::request('get', GoCardless_Client::$base_url . $path, $params);
	}
	
	/**
	 * Configure a POST request
	 *
	 * @param string $path The URL to make the request to
	 * @param array $data The parameters to use for the POST body
	 *
	 * @return string The response text
	 */
	public static function apiPost($path, $data = array()) {
		return GoCardless_Client::request('post', GoCardless_Client::$base_url . $path, $data);
	}
	
	// api_put
	
	/**
	 * Returns the merchant associated with the client's access token
	 *
	 * @param string $id The id of the merchant to fetch
	 *
	 * @return object The merchant object
	 */
	public static function merchant($id) {
		
		if (!isset(GoCardless::$account_details['access_token'])) {
			throw new GoCardlessClientException('Access token missing');
		}
		
		return new GoCardless_Merchant($id);
		
	}
	
	/**
	 * Get a specific subscription
	 *
	 * @param string $id The id of the subscription to fetch
	 *
	 * @return object The subscription matching the id requested
	 */
	public static function subscription($id) {
		
		if (!isset(GoCardless::$account_details['access_token'])) {
			throw new GoCardlessClientException('Access token missing');
		}
		
		return new GoCardless_Subscription($id);
		
	}
	
	/**
	 * Get a specific pre_authorization
	 *
	 * @param string $id The id of the pre_authorization to fetch
	 *
	 * @return object The pre-authorization matching the id requested
	 */
	public static function pre_authorization($id) {
		
		if (!isset(GoCardless::$account_details['access_token'])) {
			throw new GoCardlessClientException('Access token missing');
		}
		
		return new GoCardless_Pre_Authorization($id);
		
	}
	
	/**
	 * Get a specific user
	 *
	 * @param string $id The id of the user to fetch
	 *
	 * @return object The user object matching the id requested
	 */
	public static function user($id) {
		
		if (!isset(GoCardless::$account_details['access_token'])) {
			throw new GoCardlessClientException('Access token missing');
		}
		
		return new GoCardless_User($id);
		
	}
	
	/**
	 * Get a specific bill
	 *
	 * @param string $id The id of the bill to fetch
	 *
	 * @return object The bill object matching the id requested
	 */
	public static function bill($id) {
		
		if (!isset(GoCardless::$account_details['access_token'])) {
			throw new GoCardlessClientException('Access token missing');
		}
		
		return new GoCardless_Bill($id);
		
	}
	
	/**
	 * Get a specific payment
	 *
	 * @param string $id The id of the payment to fetch
	 *
	 * @return object The payment object matching the id requested
	 */
	public static function payment($id) {
		
		if (!isset(GoCardless::$account_details['access_token'])) {
			throw new GoCardlessClientException('Access token missing');
		}
		
		return new GoCardless_Payment($id);
		
	}
	
	/**
	 * Create a new bill under a given pre-authorization
	 *
	 * @param array $attrs Must include pre_authorization_id and amount
	 *
	 * @return string The new bill object
	 */
	public function createBill($attrs) {
	
		// # Create a new bill under a given pre-authorization
    	// # @see PreAuthorization#create_bill
    	// #
    	// # @param [Hash] attrs must include +:pre_authorization_id+ and +:amount+
    	// # @return [Bill] the created bill object
    	// def create_bill(attrs)
    	//   Bill.new_with_client(self, attrs).save
    	// end
		
		// ? return GoCardless_Bill::new($attrs);
		
		
		return GoCardless_Bill::create($attrs);
		
	}
	
	/**
	 * Generate a URL to give a user to create a new subscription
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function newSubscriptionUrl($params) {
		return GoCardless_Client::newLimitUrl('subscription', $params);
	}
	
	/**
	 * Generate a URL to give a user to create a new pre-authorized payment
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function newPreAuthorizationUrl($params) {
		return GoCardless_Client::newLimitUrl('pre_authorization', $params);
	}
	
	/**
	 * Generate a URL to give a user to create a new bill
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function newBillUrl($params) {
		return GoCardless_Client::newLimitUrl('bill', $params);
	}
	
	/**
	 * Send an HTTP request to confirm the creation of a new payment resource
	 *
	 * @param array $params Parameters to send with the request
	 *
	 * @return string The result of the HTTP request
	 */
	public static function confirmResource($params) {
		
		// Define confirm endpoint
		$endpoint = '/confirm';
		
		// First validate signature
		// Then send confirm request
		
		// List of required params
		$required_params = array(
			'resource_id', 'resource_type'
		);
		
		// Loop through required params
		// Add to $data or throw exception if missing
		foreach ($required_params as $key => $value) {
			if (!isset($params[$value])) {
				throw new GoCardlessArgumentsException("$value missing");
			}
			$data[$value] = $params[$value];
		}
		
		// state is optional
		if (isset($params['state'])) {
			$data['state'] = $params['state'];
		}
		
		// resource_uri is optional
		if (isset($params['resource_uri'])) {
			$data['resource_uri'] = $params['resource_uri'];
		}
		
		$sig_validation_data = array(
			'data'		=> $data,
			'secret'	=> GoCardless::$account_details['app_secret'],
			'signature'	=> $params['signature']
		);
		
		if (GoCardless_Client::validateSignature($sig_validation_data) == false) {
			throw new GoCardlessSignatureException();
		}
		
		// Sig valid, now send confirm request
		$confirm_params = array(
			'resource_id'	=> $params['resource_id'],
			'resource_type'	=> $params['resource_type']
		);
		
		// Use HTTP Basic Authorization
		$confirm_params['headers']['http_authorization'] = true;
		
		// If no method-specific redirect sent, use class level if available
		if (!isset($params['redirect_uri']) && isset($this)) {
			$confirm_params['redirect_uri'] = $this->redirect_uri;
		}
		
		// Do query
		$confirm = GoCardless_Client::apiPost(GoCardless_Client::$api_path . $endpoint, $confirm_params);
		
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
	public static function validateWebhook($params) {
		
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
		
		return GoCardless_Client::validateSignature($data);
		
	}
	
	/**
	 * Makes an HTTP request
	 *
	 * @param string $method The method to use for the request
	 * @param string $path The API path to make the request to
	 * @param array $opts The parameters to use for the request
	 *
	 * @return string The response text
	 */
	protected static function request($method, $path, $opts = array()) {
		
		$ch = curl_init($path);
		
		$curl_options = array(
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 60
		);
		
		// Request format
		$curl_options[CURLOPT_HTTPHEADER][] = 'Accept: application/json';
		
		// HTTP Authentication (for confirming new payments)
		if (isset($opts['headers']['http_authorization']) && $opts['headers']['http_authorization'] == true) {
			$curl_options[CURLOPT_USERPWD] = GoCardless::$account_details['app_id'] . ':' . GoCardless::$account_details['app_secret'];
			unset($opts['headers']['http_authorization']);
		}
		
		// Authentication (for API requests)
		if (isset($opts['headers']['authorization']) && $opts['headers']['authorization'] == true) {
			$curl_options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . GoCardless::$account_details['access_token'];
			unset($opts['headers']['authorization']);
		}
		
		if ($method == 'post') {

			$curl_options[CURLOPT_POST] = 1;

			if (isset($opts)) {
				$curl_options[CURLOPT_POSTFIELDS] = http_build_query($opts, null, '&');
			}
			
		} elseif ($method == 'get') {
			
			$curl_options[CURLOPT_HTTPGET] = 1;
			
		}
		
		// Debug
		//if ($method == 'post') {
		//	// POST request, so show url and vars
		//	$vars = htmlspecialchars(print_r($curl_options[CURLOPT_POSTFIELDS], true));
		//	echo "<pre>\n\nRequest\n\nPOST: $path\n";
		//	echo "Post vars sent:\n";
		//	echo "$vars\n";
		//	echo "Full curl vars:\n";
		//	print_r($curl_options);
		//	echo '</pre>';
		//} elseif ($method == 'get') {
		//	// GET request, so show just show url
		//	echo "<pre>\n\nRequest\nGET: $path\n";
		//	echo "Full curl vars: ";
		//	print_r($curl_options);
		//	echo '</pre>';
		//} else {
		//	echo "Method not set!";
		//}
		
		curl_setopt_array($ch, $curl_options);
		
		$result = curl_exec($ch);
		
		// Debug
		//echo "<pre>\nCurl result: ";
		//print_r(curl_getinfo($ch));
		//echo "</pre>";
		
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
	 * @param array $params Should include data, secret and signature
	 *
	 * @return boolean True or false
	 */
	public static function validateSignature($params) {
		
		$new_sig = Utils::generateSignature($params['data'], $params['secret']);
		
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
	public static function generateNonce() {
		
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
	 * @param string $type Payment type
	 * @param string $limit_params The specific parameters for this payment
	 *
	 * @return string The new payment URL
	 */
	private static function newLimitUrl($type, $limit_params) {
		
		// If no method-specific redirect submitted then
		// use class level if available
		if (!isset($limit_params['redirect_uri']) && isset($this)) {
			$limit_params['redirect_uri'] = $this->redirect_uri;
		}
		
		// Add in merchant id
		$limit_params['merchant_id'] = GoCardless::$account_details['merchant_id'];
		
		// Add passed params to an array named by type
		$limit_params = array($type => $limit_params);
		
		// Merge passed and mandatory params
		$request = array_merge($limit_params, Utils::generateMandatoryParams());
		
		// Generate signature
		$request['signature'] = Utils::generateSignature($request, GoCardless::$account_details['app_secret']);

		// Generate query string from all parameters
		$query_string = Utils::generateQueryString($request);
		
		// Generate url NB. Pluralises resource
		$url = GoCardless_Client::$base_url . '/connect/' . $type . 's/new?' . $query_string;
		
		// Return the result
		return $url;
		
	}

}

?>