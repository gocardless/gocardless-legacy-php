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

    return hash_hmac('sha256',
      GoCardless_Utils::generate_query_string($params), $key);

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
  public static function generate_query_string($params, &$pairs = array(),
    $namespace = null) {

    if (is_array($params)) {

      foreach ($params as $k => $v) {

        if (is_int($k)) {
          GoCardless_Utils::generate_query_string($v, $pairs, $namespace .
            '[]');
        } else {
          GoCardless_Utils::generate_query_string($v, $pairs,
            $namespace !== null ? $namespace . "[$k]" : $k);
        }

      }

      if ($namespace !== null) {
        return $pairs;
      }

      if (empty($pairs)) {
        return '';
      }

      sort($pairs);

      $strs = array();
      foreach ($pairs as $pair) {
        $strs[] = $pair[0] . '=' . $pair[1];
      }

      return implode('&', $strs);

    } else {

      $pairs[] = array(rawurlencode($namespace), rawurlencode($params));

    }

  }

  /**
   * Strip underscores and convert to CamelCaps
   *
   * @param string $string The string to process
   *
   * @return string The result
   */
  public static function camelize($string) {

    return implode(array_map('ucfirst', explode('_', $string)));

  }

  /**
   * Convert a word to the singular
   *
   * @param string $string The string to process
   *
   * @return string The result
   */
  public static function singularize($string) {

    if (substr($string, -1) == 's') {
      return substr($string, 0, -1);
    } elseif (substr($string, -1) == 'i') {
      return substr($string, 0, -1) . 'us';
    } else {
      return $string;
    }

  }

}
