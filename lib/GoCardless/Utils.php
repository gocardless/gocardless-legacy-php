<?php

/**
 * GoCardless utility functions
 *
 * @package GoCardless\Utils
 */

/**
 * GoCardless utils class
 *
 */
class GoCardless_Utils {

  /**
   * Generate a signature for a request given the app secret
   *
   * @param array $params The parameters to generate a signature for
   * @param string $key The key to generate the signature with
   *
   * @return string A URL-encoded string of parameters
   */
  public static function generate_signature($params, $key) {

    return hash_hmac('sha256', GoCardless_Utils::generate_query_string($params), $key);

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
  public static function generate_query_string($params, &$pairs = array(), $namespace = null) {

    if (is_array($params)) {

      foreach ($params as $k => $v) {

        if (is_int($k)) {
          GoCardless_Utils::generate_query_string($v, $pairs, $namespace . '[]');
        } else {
          GoCardless_Utils::generate_query_string($v, $pairs, $namespace !== null ? $namespace . "[$k]" : $k);
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

}
