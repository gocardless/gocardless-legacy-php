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
    'production'  => 'https://gocardless.com',
    'sandbox'     => 'https://sandbox.gocardless.com'
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

    $endpoint = '/oauth/authorize';

    $url =  GoCardless_Client::$base_url . $endpoint .
        '?client_id='. urlencode($this->account_details['app_id']) .
        '&redirect_uri=' . urlencode($options['redirect_uri']) .
        '&scope=manage_merchant' .
        '&response_type=code';

    return $url;

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

    $path = '/oauth/access_token';

    $options['http_authorization'] = $this->account_details['app_id'] . ':' . $this->account_details['app_secret'];

    $response = $this->request('post', $path, $options);

    $merchant = explode(':', $response['scope']);
    $merchant_id = $merchant[1];
    $access_token = $response['access_token'];

    $return = array(
      'merchant_id'   => $merchant_id,
      'access_token'  => $access_token
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
  public function apiGet($path, $params = array()) {

    $path = GoCardless_Client::$api_path . $path;

    return $this->request('get', $path, $params);

  }

  /**
   * Configure a POST request
   *
   * @param string $path The URL to make the request to
   * @param array $data The parameters to use for the POST body
   *
   * @return string The response text
   */
  public function apiPost($path, $data = array()) {

    $path = GoCardless_Client::$api_path . $path;

    return $this->request('post', $path, $data);

  }

  /**
   * Configure a PUT request
   *
   * @param string $path The URL to make the request to
   * @param array $data The parameters to use for the PUT body
   *
   * @return string The response text
   */
  public function apiPut($path, $data = array()) {

    $path = GoCardless_Client::$api_path . $path;

    return $this->request('put', $path, $data);

  }

  /**
   * Returns the merchant associated with the client's access token
   *
   * @param string $id The id of the merchant to fetch
   *
   * @return object The merchant object
   */
  public function merchant($id) {

    if (!isset($this->account_details['access_token'])) {
      throw new GoCardlessClientException('Access token missing');
    }

    return GoCardless_Merchant::findWithClient($this, $id);

  }

  /**
   * Get a specific subscription
   *
   * @param string $id The id of the subscription to fetch
   *
   * @return object The subscription matching the id requested
   */
  public function subscription($id) {

    if (!isset($this->account_details['access_token'])) {
      throw new GoCardlessClientException('Access token missing');
    }

    return GoCardless_Subscription::findWithClient($this, $id);

  }

  /**
   * Get a specific pre_authorization
   *
   * @param string $id The id of the pre_authorization to fetch
   *
   * @return object The pre-authorization matching the id requested
   */
  public function pre_authorization($id) {

    if (!isset($this->account_details['access_token'])) {
      throw new GoCardlessClientException('Access token missing');
    }

    return GoCardless_PreAuthorization::findWithClient($this, $id);

  }

  /**
   * Get a specific user
   *
   * @param string $id The id of the user to fetch
   *
   * @return object The user object matching the id requested
   */
  public function user($id) {

    if (!isset($this->account_details['access_token'])) {
      throw new GoCardlessClientException('Access token missing');
    }

    return GoCardless_User::findWithClient($this, $id);

  }

  /**
   * Get a specific bill
   *
   * @param string $id The id of the bill to fetch
   *
   * @return object The bill object matching the id requested
   */
  public function bill($id) {

    if (!isset($this->account_details['access_token'])) {
      throw new GoCardlessClientException('Access token missing');
    }

    return GoCardless_Bill::findWithClient($this, $id);

  }

  /**
   * Create a new bill under a given pre-authorization
   *
   * @param array $attrs Must include pre_authorization_id and amount
   *
   * @return string The new bill object
   */
  public function createBill($attrs) {

    if (!isset($this->account_details['access_token'])) {
      throw new GoCardlessClientException('Access token missing');
    }

    if (!isset($attrs['id'])) {
      throw new GoCardlessArgumentsException('Bill id missing');
    }

    $bill = new GoCardless_Bill($this, $attrs['id']);

    return $bill->create($attrs);

  }

  /**
   * Generate a URL to give a user to create a new subscription
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public function newSubscriptionUrl($params) {
    return $this->newLimitUrl('subscription', $params);
  }

  /**
   * Generate a URL to give a user to create a new pre-authorized payment
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public function newPreAuthorizationUrl($params) {
    return $this->newLimitUrl('pre_authorization', $params);
  }

  /**
   * Generate a URL to give a user to create a new bill
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public function newBillUrl($params) {
    return $this->newLimitUrl('bill', $params);
  }

  /**
   * Send an HTTP request to confirm the creation of a new payment resource
   *
   * @param array $params Parameters to send with the request
   *
   * @return string The result of the HTTP request
   */
  public function confirmResource($params) {

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
      'data'      => $data,
      'secret'    => $this->account_details['app_secret'],
      'signature' => $params['signature']
    );

    if ($this->validateSignature($sig_validation_data) == false) {
      throw new GoCardlessSignatureException();
    }

    // Sig valid, now send confirm request
    $confirm_params = array(
      'resource_id'   => $params['resource_id'],
      'resource_type' => $params['resource_type']
    );

    // Use HTTP Basic Authorization
    $confirm_params['http_authorization'] = $this->account_details['app_id'] . ':' . $this->account_details['app_secret'];

    // If no method-specific redirect sent, use class level if available
    if (!isset($params['redirect_uri']) && isset($this)) {
      $confirm_params['redirect_uri'] = $this->redirect_uri;
    }

    // Do query
    $raw_response = $this->apiPost(GoCardless_Client::$api_path . $endpoint, $confirm_params);

    // Decode response
    $response = json_decode($raw_response, true);

    if ($response['success'] == true) {

      $endpoint = '/' . $params['resource_type'] . 's/' . $params['resource_id'];

      return Utils::fetchResource($endpoint);

    } else {

      return false;

    }

  }

  /**
   * Test whether a webhook is valid or not
   *
   * @param array params The contents of the webhook in array form
   *
   * @return boolean If valid returns true
   */
  public function validateWebhook($params) {

    $sig = $params['payload']['signature'];
    unset($params['payload']['signature']);

    if (!isset($sig)) {
      return false;
    }

    $data = array(
      'data'      => $params['payload'],
      'secret'    => $this->account_details['app_secret'],
      'signature' => $sig
    );

    return $this->validateSignature($data);

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
  protected function request($method, $path, $opts = array()) {

    $path = GoCardless_Client::$base_url . $path;

    $ch = curl_init($path);

    $curl_options = array(
      CURLOPT_CONNECTTIMEOUT  => 10,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_TIMEOUT         => 60
    );

    // Request format
    $curl_options[CURLOPT_HTTPHEADER][] = 'Accept: application/json';

    $authorization = $this->account_details['access_token'];

    // HTTP Authentication (for confirming new payments)
    if (isset($opts['http_authorization'])) {

      $curl_options[CURLOPT_USERPWD] = $opts['http_authorization'];
      unset($opts['http_authorization']);

    } else {

      if (!isset($this->account_details['access_token'])) {
        throw new GoCardlessClientException('Access token missing');
      }

      $curl_options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $this->account_details['access_token'];

    }

    if ($method == 'post') {

      $curl_options[CURLOPT_POST] = 1;

      if (isset($opts)) {
        $curl_options[CURLOPT_POSTFIELDS] = http_build_query($opts, null, '&');
      }

    } elseif ($method == 'get') {

      $curl_options[CURLOPT_HTTPGET] = 1;

    } elseif ($method == 'put') {

      $curl_options[CURLOPT_PUT] = 1;

    }

    // Debug
    //if ($method == 'post') {
    //  // POST request, so show url and vars
    //  $vars = htmlspecialchars(print_r($curl_options[CURLOPT_POSTFIELDS], true));
    //  echo "<pre>\n\nRequest\n\nPOST: $path\n";
    //  echo "Post vars sent:\n";
    //  echo "$vars\n";
    //  echo "Full curl vars:\n";
    //  print_r($curl_options);
    //  echo '</pre>';
    //} elseif ($method == 'get') {
    //  // GET request, so show just show url
    //  echo "<pre>\n\nRequest\nGET: $path\n";
    //  echo "Full curl vars: ";
    //  print_r($curl_options);
    //  echo '</pre>';
    //} else {
    //  echo "Method not set!";
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

    $object = json_decode($result, true);

    return $object;

  }

  // Sign params

  /**
   * Confirm whether a signature is valid
   *
   * @param array $params Should include data, secret and signature
   *
   * @return boolean True or false
   */
  public function validateSignature($params) {

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
  public function generateNonce() {

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
  private function newLimitUrl($type, $limit_params) {

    // If no method-specific redirect submitted then
    // use class level if available
    if (!isset($limit_params['redirect_uri']) && isset($this)) {
      $limit_params['redirect_uri'] = $this->redirect_uri;
    }

    // Add in merchant id
    $limit_params['merchant_id'] = $this->account_details['merchant_id'];

    // Add passed params to an array named by type
    $limit_params = array($type => $limit_params);

    // Merge passed and mandatory params
    $request = array_merge($limit_params, $this->generateMandatoryParams());

    // Generate signature
    $request['signature'] = Utils::generateSignature($request, $this->account_details['app_secret']);

    // Generate query string from all parameters
    $query_string = Utils::generateQueryString($request);

    // Generate url NB. Pluralises resource
    $url = GoCardless_Client::$base_url . '/connect/' . $type . 's/new?' . $query_string;

    // Return the result
    return $url;

  }

  /**
   * Generate mandatory payment parameters: client_id, nonce and timestamp
   *
   * @return array Mandatory payment parameters
   */
  public function generateMandatoryParams() {

    // Create new UTC date object
    $date = new DateTime(null, new DateTimeZone('UTC'));

    $request = array(
      'client_id' => $this->account_details['app_id'],
      'nonce'     => GoCardless_Client::generateNonce(),
      'timestamp' => $date->format('Y-m-d\TH:i:s\Z')
    );

    return $request;

  }

}

?>