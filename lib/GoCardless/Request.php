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

    // Initialize curl
    $ch = curl_init();

    // Default curl options, including library & version number
    $curl_options = array(
      CURLOPT_CONNECTTIMEOUT  => 10,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_TIMEOUT         => 60,
      CURLOPT_USERAGENT       => 'gocardless-php/v' . GoCardless::VERSION,
    );

    // Set application specific user agent suffix if found
    if (isset($params['ua_tag'])) {

      // Set the user agent header
      $curl_options[CURLOPT_USERAGENT] .= ' ' . $params['ua_tag'];

      // Remove ua_tag from $params as $params are used later
      unset($params['ua_tag']);

    }

    // Request format
    $curl_options[CURLOPT_HTTPHEADER][] = 'Accept: application/json';

    // Enable SSL certificate validation.
    // This is true by default since libcurl 7.1.
    $curl_options[CURLOPT_SSL_VERIFYPEER] = true;

    // Debug - DO NOT USE THIS IN PRODUCTION FOR SECURITY REASONS
    //
    // This fixes a problem in some environments with connecting to HTTPS-enabled servers.
    // Sometimes, Curl has no list of valid CAs, and so won't connect. With this fix, it
    // doesn't verify and just connects anyway, instead of throwing an exception.
    //
    //$curl_options[CURLOPT_SSL_VERIFYPEER] = false;

    // HTTP Authentication (for confirming new payments)
    if (isset($params['http_authorization'])) {

      // Set HTTP Basic Authorization header
      $curl_options[CURLOPT_USERPWD] = $params['http_authorization'];

      // Unset http basic param as params are used later
      unset($params['http_authorization']);

    } else {

      // Throw an exception if access token is missing
      if ( ! isset($params['http_bearer'])) {
        throw new GoCardless_ClientException('Access token missing');
      }

      // Set the authorization header
      $curl_options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' .
        $params['http_bearer'];

      // Unset http_bearer param as params are used later
      unset($params['http_bearer']);

    }

    if ($method == 'post') {
      // Curl options for POSt

      $curl_options[CURLOPT_POST] = 1;

      if ( ! empty($params)) {
        $curl_options[CURLOPT_POSTFIELDS] = http_build_query($params, null,
          '&');
      }

    } elseif ($method == 'get') {
      // Curl options for GET

      $curl_options[CURLOPT_HTTPGET] = 1;

      if ( ! empty($params)) {
        $url .= '?' . http_build_query($params, null, '&');
      }

    } elseif ($method == 'put') {
      // Curl options for PUT

      $curl_options[CURLOPT_PUT] = 1;

      // Receiving the following Curl error?:
      //    "cannot represent a stream of type MEMORY as a STDIO FILE*"
      // Try changing the first parameter of fopen() to `php://temp`
      $fh = fopen('php://memory', 'rw+');

      $curl_options[CURLOPT_INFILE] = $fh;
      $curl_options[CURLOPT_INFILESIZE] = 0;

    }

    // Set the url to query
    curl_setopt($ch, CURLOPT_URL, $url);

    // Debug
    //echo "<pre>\nCurl " . strtoupper($method) . " request to: $url\n";
    //if (isset($curl_options[CURLOPT_POSTFIELDS])) {
    //  echo "Post vars:\n";
    //  echo htmlspecialchars(print_r($curl_options[CURLOPT_POSTFIELDS], true));
    //  echo "\n";
    //}
    //echo "Curl request config:\n";
    //print_r($curl_options);
    //echo '</pre>';

    // Set curl options
    curl_setopt_array($ch, $curl_options);

    // Send the request
    $result = curl_exec($ch);
    $error = curl_errno($ch);

    if ($error == CURLE_SSL_PEER_CERTIFICATE || $error == CURLE_SSL_CACERT ||
        $error == 77) {
      curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cert-bundle.crt');
      $result = curl_exec($ch);
    }

    // Debug
    //echo "<pre>\nCurl result config:\n";
    //print_r(curl_getinfo($ch));
    //echo "Curl result:\n";
    //echo htmlspecialchars($result) . "\n";
    //echo "</pre>";

    // Grab the response code and throw an exception if it's not good
    $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_response_code < 200 || $http_response_code > 300) {

      // Create a string
      $message = print_r(json_decode($result, true), true);

      // Throw an exception with the error message
      throw new GoCardless_ApiException($message, $http_response_code, $result);

    }

    // Close the connection
    curl_close($ch);

    // Close the $fh handle used by PUT
    if (isset($fh)) {
      fclose($fh);
    }

    // Return the response as an array
    return json_decode($result, true);

  }

}
