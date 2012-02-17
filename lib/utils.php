<?php

/**
 * GoCardless utility functions
 *
 */

/**
 * GoCardless utils class
 *
 */
class Utils {

  /**
   * Generate a signature for a request given the app secret
   *
   * @param array $params The parameters to generate a signature for
   * @param string $key The key to generate the signature with
   *
   * @return string A URL-encoded string of parameters
   */
  public static function generateSignature($params, $key) {

    return hash_hmac('sha256', Utils::generateQueryString($params), $key);

  }

  /**
   * Generates, encodes, re-orders variables for the query string.
   *
   * @param array $params The specific parameters for this payment
   * @param array $pairs Pairs
   * @param string $namespace The namespace
   *
   * @return string An encoded string of parameters
   */
  public static function generateQueryString($params, &$pairs = array(), $namespace = null) {

    if (is_array($params)) {

      foreach ($params as $k => $v) {

        if (is_int($k)) {
          Utils::generateQueryString($v, $pairs, $namespace . '[]');
        } else {
          Utils::generateQueryString($v, $pairs, $namespace !== null ? $namespace . "[$k]" : $k);
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
   * Fetches a resource from an API endpoint
   *
   * @param string $endpoint The endpoint to send the request to
   * @param string $method The method to use to send the request
   *
   * @return object The returned resource
   */
  public static function fetchResource($endpoint, $method = 'get') {

    // Do query
    if ($method == 'get') {
      $result = GoCardless::$client->apiGet(GoCardless_Client::$api_path . $endpoint, $params);
    } else {
      $result = GoCardless::$client->apiPost(GoCardless_Client::$api_path . $endpoint, $params);
    }

    return $result;

  }

}

?>