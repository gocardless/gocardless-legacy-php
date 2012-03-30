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
class GoCardless_Request {

  /**
   * Configure a GET request
   *
   * @param string $url The URL to make the request to
   * @param array $params The parameters to use for the POST body
   *
   * @return string The response text
   */
  public static function get($url, $params = array()) {
    return self::call('get', $url, $params);
  }

  /**
   * Configure a POST request
   *
   * @param string $url The URL to make the request to
   * @param array $params The parameters to use for the POST body
   *
   * @return string The response text
   */
  public static function post($url, $params = array()) {
    return self::call('post', $url, $params);
  }

  /**
   * Configure a PUT request
   *
   * @param string $url The URL to make the request to
   * @param array $params The parameters to use for the PUT body
   *
   * @return string The response text
   */
  public static function put($url, $params = array()) {
    return self::call('put', $url, $params);
  }

  /**
   * Makes an HTTP request
   *
   * @param string $method The method to use for the request
   * @param string $url The API url to make the request to
   * @param array $params The parameters to use for the request
   *
   * @return string The response text
   */
  protected static function call($method, $url, $params = array()) {

    $ch = curl_init($url);

    $curl_options = array(
      CURLOPT_CONNECTTIMEOUT  => 10,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_TIMEOUT         => 60,
      CURLOPT_USERAGENT       => 'gocardless-php-v' . GoCardless::VERSION
    );

    // Request format
    $curl_options[CURLOPT_HTTPHEADER][] = 'Accept: application/json';

    // HTTP Authentication (for confirming new payments)
    if (isset($params['http_authorization'])) {

      $curl_options[CURLOPT_USERPWD] = $params['http_authorization'];
      unset($params['http_authorization']);

    } else {

      if ( ! isset($params['http_bearer'])) {
        throw new GoCardless_ClientException('Access token missing');
      }

      $curl_options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' .
        $params['http_bearer'];
      unset($params['http_bearer']);

    }

    if ($method == 'post') {

      $curl_options[CURLOPT_POST] = 1;

      if (isset($params)) {
        $curl_options[CURLOPT_POSTFIELDS] = http_build_query($params, null,
          '&');
      }

    } elseif ($method == 'get') {

      $curl_options[CURLOPT_HTTPGET] = 1;

    } elseif ($method == 'put') {

      $curl_options[CURLOPT_PUT] = 1;
      $fh = fopen('php://memory', 'rw+');
      $curl_options[CURLOPT_INFILE] = $fh;
      $curl_options[CURLOPT_INFILESIZE] = 0;

    }

    // Debug
    //if ($method == 'post') {
    //  // POST request, so show url and vars
    //  $vars = htmlspecialchars(print_r($curl_options[CURLOPT_POSTFIELDS], true));
    //  echo "<pre>\n\nRequest\n\nPOST: $url\n";
    //  echo "Post vars sent:\n";
    //  echo "$vars\n";
    //  echo "Full curl vars:\n";
    //  print_r($curl_options);
    //  echo '</pre>';
    //} elseif ($method == 'get') {
    //  // GET request, so show just show url
    //  echo "<pre>\n\nRequest\nGET: $url\n";
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

    // Grab the response code and throw an exception if it's not good
    $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_response_code < 200 || $http_response_code > 300) {

      $response = json_decode($result, true);

      // Convert json blob API error messages into a readable string
      // One layer of recursion due to arbitrary keys
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

    if (isset($fh)) {
      fclose($fh);
    }

    return json_decode($result, true);

  }

}
