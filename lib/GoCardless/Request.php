<?php

/**
 * GoCardless request functions
 *
 * @package GoCardless\Request
 */

/**
 * GoCardless request class
 *
 */
class GoCardless_Request extends GoCardless {

  /**
   * Configure a GET request
   *
   * @param string $path The URL to make the request to
   * @param array $params The parameters to use for the POST body
   *
   * @return string The response text
   */
  public static function get($path, $params = array()) {

    $path = GoCardless_Client::$api_path . $path;

    return self::request('get', $path, $params);

  }

  /**
   * Configure a POST request
   *
   * @param string $path The URL to make the request to
   * @param array $data The parameters to use for the POST body
   *
   * @return string The response text
   */
  public static function post($path, $data = array()) {

    $path = GoCardless_Client::$api_path . $path;

    return self::request('post', $path, $data);

  }

  /**
   * Configure a PUT request
   *
   * @param string $path The URL to make the request to
   * @param array $data The parameters to use for the PUT body
   *
   * @return string The response text
   */
  public static function put($path, $data = array()) {

    $path = GoCardless_Client::$api_path . $path;

    return self::request('put', $path, $data);

  }

  /**
   * Fetch an access token for the current user
   *
   * @param array $options The parameters to use
   *
   * @return string The access token
   */
  public static function fetch_access_token($options){

    if (!isset($options['redirect_uri'])) {
      throw new GoCardless_ArgumentsException('redirect_uri required');
    }

    $path = '/oauth/access_token';

    var_dump(get_class_vars());

    $path = GoCardless_Client::$api_path . $path;

    $options['http_authorization'] = GoCardless::$client->account_details['app_id'] . ':' . GoCardless::$client->account_details['app_secret'];

    $response = self::request('post', $path, $options);

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
   * Makes an HTTP request
   *
   * @param string $method The method to use for the request
   * @param string $path The API path to make the request to
   * @param array $opts The parameters to use for the request
   *
   * @return string The response text
   */
  protected static function request($method, $path, $opts = array()) {

    $path = GoCardless::$client->base_url . $path;

    $ch = curl_init($path);

    $curl_options = array(
      CURLOPT_CONNECTTIMEOUT  => 10,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_TIMEOUT         => 60
    );

    // Request format
    $curl_options[CURLOPT_HTTPHEADER][] = 'Accept: application/json';

    $authorization = GoCardless::$client->account_details['access_token'];

    // HTTP Authentication (for confirming new payments)
    if (isset($opts['http_authorization'])) {

      $curl_options[CURLOPT_USERPWD] = $opts['http_authorization'];
      unset($opts['http_authorization']);

    } else {

      if (!isset(GoCardless::$client->account_details['access_token'])) {
        throw new GoCardless_ClientException('Access token missing');
      }

      $curl_options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . GoCardless::$client->account_details['access_token'];

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
    if ($method == 'post') {
      // POST request, so show url and vars
      $vars = htmlspecialchars(print_r($curl_options[CURLOPT_POSTFIELDS], true));
      echo "<pre>\n\nRequest\n\nPOST: $path\n";
      echo "Post vars sent:\n";
      echo "$vars\n";
      echo "Full curl vars:\n";
      print_r($curl_options);
      echo '</pre>';
    } elseif ($method == 'get') {
      // GET request, so show just show url
      echo "<pre>\n\nRequest\nGET: $path\n";
      echo "Full curl vars: ";
      print_r($curl_options);
      echo '</pre>';
    } else {
      echo "Method not set!";
    }

    curl_setopt_array($ch, $curl_options);

    $result = curl_exec($ch);

    // Debug
    echo "<pre>\nCurl result: ";
    print_r(curl_getinfo($ch));
    echo "</pre>";

    // Grab the response code and throw an exception if it's not good
    $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_response_code < 200 || $http_response_code > 300) {

      $response = json_decode($result, true);

      // Urgh
      $message = '';
      if (is_array($response)) {
        foreach ($response as $key => $value) {
          if (is_array($value)) {
            foreach ($value as $key2 => $value2) {
              $message .= $key2 . ' : ' . $value2 . '. ';
            }
          } else {
            $message .= $key . ' : ' . $value . '. ';
          }
        }
      }

      throw new GoCardless_ApiException($message, $http_response_code);

    }

    curl_close($ch);

    $object = json_decode($result, true);

    return $object;

  }

}
