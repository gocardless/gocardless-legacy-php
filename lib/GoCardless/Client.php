<?php

/**
 * GoCardless client functions
 *
 * @package GoCardless\Client
 */

/**
 * GoCardless client class
 *
 */
class GoCardless_Client {

  /**
   * Account details for this instance of GoCardless_Client
   *
   * @var array $account_details
   */
  public $account_details;

  /**
   * The base_url for the API to use for all client requests
   *
   * @var string $base_url
   */
  public $base_url;

  /**
   * Array of possible base_urls to use
   *
   * @var array $base_urls
   */
  public static $base_urls = array(
    'production'  => 'https://gocardless.com',
    'sandbox'     => 'https://sandbox.gocardless.com'
  );

  /**
   * The path to use to call the API
   *
   * @var string $api_path
   */
  public static $api_path = '/api/v1';

  /**
   * The url to redirect the user to
   *
   * @var string $redirect_uri
   */
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
    if ( ! isset($this->account_details['app_id'])) {
      throw new GoCardless_ClientException('No app_id specified');
    }

    // Check for app_secret
    if ( ! isset($this->account_details['app_secret'])) {
      throw new GoCardless_ClientException('No app_secret specfied');
    }

    // If environment is not set then default to production
    if ( ! isset(GoCardless::$environment)) {
      GoCardless::$environment = 'production';
    }

    // Take base_url from array
    if (isset($account_details['base_url'])) {
      $this->base_url = $account_details['base_url'];
      unset($account_details['base_url']);

    } else {
      // Otherwise set it based on environment
      $this->base_url = self::$base_urls[GoCardless::$environment];
    }

  }

  /**
   * Generate the OAuth authorize url
   *
   * @param array $options The parameters to use
   *
   * @return string The generated url
   */
  public function authorize_url($options = null) {

    if ( ! isset($options['redirect_uri'])) {
      throw new GoCardless_ArgumentsException('redirect_uri required');
    }

    $endpoint = '/oauth/authorize';

    return $this->base_url . $endpoint .
        '?client_id='. urlencode($this->account_details['app_id']) .
        '&redirect_uri=' . urlencode($options['redirect_uri']) .
        '&scope=manage_merchant' .
        '&response_type=code';

  }

  /**
   * Returns the merchant associated with the client's access token
   *
   * @param string $id The id of the merchant to fetch
   *
   * @return object The merchant object
   */
  public function merchant($id = null) {

    if ($id == null) {
      $id = $this->account_details['merchant_id'];
    }

    return GoCardless_Merchant::find_with_client($this, $id);

  }

  /**
   * Make a request to the API
   *
   * @param string $method The request method to use
   * @param string $endpoint The API endpoint to call
   * @param string $params The parameters to send with the request
   *
   * @return object The returned object
   */
  public function request($method, $endpoint, $params = array()) {

    // If there is no http_authorization, try checking for access_token
    if ( ! isset($params['http_authorization'])) {

      // No http_authorization and no access_token? Fail
      if ( ! isset($this->account_details['access_token'])) {
        throw new GoCardless_ClientException('Access token missing');
      }

      // access_token found so set Authorization header to contain bearer
      $params['http_bearer'] = $this->account_details['access_token'];

    }

    if (substr($endpoint, 0, 6) == '/oauth') {

      // OAuth API calls don't require /api/v1 base
      $url = $this->base_url . $endpoint;

    } else {

      // http://sandbox.gocardless.com | /api/v1 | /test
      $url = $this->base_url . self::$api_path . $endpoint;

    }

    // Call the Request library (might be aliases for testing) with URL and params
    return call_user_func(GoCardless::getClass('Request').'::'.$method, $url, $params);

  }

  /**
   * Fetch an access token for the current user
   *
   * @param array $params The parameters to use
   *
   * @return string The access token
   */
  public function fetch_access_token($params) {

    if ( ! isset($params['redirect_uri'])) {
      throw new GoCardless_ArgumentsException('redirect_uri required');
    }

    $params['http_authorization'] = $this->account_details['app_id'] . ':' . $this->account_details['app_secret'];

    $response = $this->request('post', '/oauth/access_token', $params);

    $merchant = explode(':', $response['scope']);
    $merchant_id = isset($merchant[1]) ? $merchant[1] : null;
    $access_token = $response['access_token'];

    return array(
      'merchant_id'   => $merchant_id,
      'access_token'  => $access_token
    );

  }

  /**
   * Get a specific subscription
   *
   * @param string $id The id of the subscription to fetch
   *
   * @return object The subscription matching the id requested
   */
  public function subscription($id) {
    return GoCardless_Subscription::find_with_client($this, $id);
  }

  /**
   * Get a specific pre_authorization
   *
   * @param string $id The id of the pre_authorization to fetch
   *
   * @return object The pre-authorization matching the id requested
   */
  public function pre_authorization($id) {
    return GoCardless_PreAuthorization::find_with_client($this, $id);
  }

  /**
   * Get a specific user
   *
   * @param string $id The id of the user to fetch
   *
   * @return object The user object matching the id requested
   */
  public function user($id) {
    return GoCardless_User::find_with_client($this, $id);
  }

  /**
   * Get a specific bill
   *
   * @param string $id The id of the bill to fetch
   *
   * @return object The bill object matching the id requested
   */
  public function bill($id) {
    return GoCardless_Bill::find_with_client($this, $id);
  }

  /**
   * Create a new bill under a given pre-authorization
   *
   * @param array $attrs Must include pre_authorization_id and amount
   *
   * @return string The new bill object
   */
  public function create_bill($attrs) {

    if ( ! isset($attrs['pre_authorization_id'])) {
      throw new GoCardless_ArgumentsException('pre_authorization_id missing');
    }

    $pre_auth_attrs = array('id' => $attrs['pre_authorization_id']);
    $pre_auth = new GoCardless_PreAuthorization($this, $pre_auth_attrs);

    return $pre_auth->create_bill($attrs);

  }

  /**
   * Generate a URL to give a user to create a new subscription
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public function new_subscription_url($params) {
    return $this->new_limit_url('subscription', $params);
  }

  /**
   * Generate a URL to give a user to create a new pre-authorized payment
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public function new_pre_authorization_url($params) {
    return $this->new_limit_url('pre_authorization', $params);
  }

  /**
   * Generate a URL to give a user to create a new bill
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public function new_bill_url($params) {
    return $this->new_limit_url('bill', $params);
  }

  /**
   * Send an HTTP request to confirm the creation of a new payment resource
   *
   * @param array $params Parameters to send with the request
   *
   * @return string The result of the HTTP request
   */
  public function confirm_resource($params) {

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
      if ( ! isset($params[$value])) {
        throw new GoCardless_ArgumentsException("$value missing");
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

    if ($this->validate_signature($sig_validation_data) == false) {
      throw new GoCardless_SignatureException();
    }

    // Sig valid, now send confirm request
    $confirm_params = array(
      'resource_id'   => $params['resource_id'],
      'resource_type' => $params['resource_type']
    );

    // Use HTTP Basic Authorization
    $confirm_params['http_authorization'] = $this->account_details['app_id'] . ':' . $this->account_details['app_secret'];

    // If no method-specific redirect sent, use class level if available
    if ( ! isset($params['redirect_uri']) && isset($this->redirect_uri)) {
      $confirm_params['redirect_uri'] = $this->redirect_uri;
    }

    // Do query
    $response = $this->request('post', $endpoint, $confirm_params);

    if ($response['success'] == true) {

      $endpoint = '/' . $params['resource_type'] . 's/' . $params['resource_id'];

      return $this->request('get', $endpoint, $params);

    } else {

      throw new GoCardless_ClientException('Failed to fetch the confirmed resource.');

    }

  }

  /**
   * Test whether a webhook is valid or not
   *
   * @param array params The contents of the webhook in array form
   *
   * @return boolean If valid returns true
   */
  public function validate_webhook($params) {

    $sig = $params['signature'];
    unset($params['signature']);

    if ( ! isset($sig)) {
      return false;
    }

    $data = array(
      'data'      => $params,
      'secret'    => $this->account_details['app_secret'],
      'signature' => $sig
    );

    return $this->validate_signature($data);

  }

  // Sign params

  /**
   * Confirm whether a signature is valid
   *
   * @param array $params Should include data, secret and signature
   *
   * @return boolean True or false
   */
  public function validate_signature($params) {

    $new_sig = GoCardless_Utils::generate_signature($params['data'], $params['secret']);

    return ($new_sig === $params['signature']);

  }

  /**
   * Generates a nonce
   *
   * @return string Base64 encoded nonce
   */
  public function generate_nonce() {

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
   * @param string $params The specific parameters for this payment
   *
   * @return string The new payment URL
   */
  private function new_limit_url($type, $params) {

    // Declare empty array
    $request = array();

    // Add in merchant id
    $params['merchant_id'] = $this->account_details['merchant_id'];

    // Define optional parameters
    $opt_params = array(
      'redirect_uri',
      'cancel_uri',
      'state'
    );

    // Loop through optional parameters
    foreach ($opt_params as $opt_param) {
      if (isset($params[$opt_param])) {
        $request[$opt_param] = $params[$opt_param];
        unset($params[$opt_param]);
      }
    }

    // If no method-specific redirect submitted then
    // use class level if available
    if ( ! isset($request['redirect_uri']) && isset($this->redirect_uri)) {
      $request['redirect_uri'] = $this->redirect_uri;
    }

    // Grab the state, if there is one, and remove it from
    // the params so it doesn't get attached to the "type" object
    if (isset($limit_params['state'])) {
      $state = $limit_params['state'];
      unset($limit_params['state']);
    }

    // Create array of payment params
    $payment_params = array($type => $params);

    // Put together all the bits: passed params inc payment params & mandatory
    $request = array_merge($request, $payment_params, $this->generate_mandatory_params());

    // Generate signature
    $request['signature'] = GoCardless_Utils::generate_signature($request, $this->account_details['app_secret']);

    // Generate query string from all parameters
    $query_string = GoCardless_Utils::generate_query_string($request);

    // Generate url NB. Pluralises resource
    return $this->base_url . '/connect/' . $type . 's/new?' . $query_string;

  }

  /**
   * Generate mandatory payment parameters: client_id, nonce and timestamp
   *
   * @return array Mandatory payment parameters
   */
  public function generate_mandatory_params() {

    // Create new UTC date object
    $date = new DateTime(null, new DateTimeZone('UTC'));

    return array(
      'client_id' => $this->account_details['app_id'],
      'nonce'     => GoCardless_Client::generate_nonce(),
      'timestamp' => $date->format('Y-m-d\TH:i:s\Z')
    );

  }

}
