<?php

/**
 * GoCardless merchant functions
 *
 * @package GoCardless\Merchant
 */

/**
 * GoCardless merchant class
 *
 */
class GoCardless_Merchant {

  /**
   * The API endpoint for merchants
   *
   * @var string $endpoint
   */
  public static $endpoint = '/merchants';

  /**
   * Instantiate a new instance of the merchant object
   *
   * @param object $client The client to use for the merchant object
   * @param array $attrs The properties of the merchant
   *
   * @return object The merchant object
   */
  function __construct($client, array $attrs = null) {

    $this->client = $client;

    if (is_array($attrs)) {
      foreach ($attrs as $key => $value) {
        $this->$key = $value;
      }
    }

  }

  /**
   * This magic method is used to call subresources ie. merchant()->users()
   *
   * @param string $method The name of the method being called
   *
   * @return array The subresource index
   */
  public function __call($method, $arguments = array()) {

    $params = isset($arguments[0]) ? $arguments[0] : array();

    // Check the subresource exists
    if (array_key_exists($method, $this->sub_resource_uris)) {

      // Return the subresource
      return $this->fetch_sub_resource($method, $params);

    }

    // Subresource doesn't exist so error out
    $class = get_class($this);
    trigger_error("Call to undefined method $class::$method()", E_USER_ERROR);

  }

  /**
   * Fetch a merchant object from the API
   *
   * @param string $id The id of the merchant to fetch
   *
   * @return object The merchant object
   */
  public static function find($id) {

    $client = GoCardless::$client;

    return new self($client, $client->request('get', self::$endpoint . '/' .
      $id));

  }

  /**
   * Fetch a merchant from the API
   *
   * @param object $client The client object to use to make the query
   * @param string $id The id of the merchant to fetch
   *
   * @return object The bill object
   */
  public static function find_with_client($client, $id) {

    return new self($client, $client->request('get', self::$endpoint . '/' .
      $id));

  }

  /**
   * Fetch an object's subresource from the API
   *
   * @param string $type The subresource to fetch
   * @param array $params The params for the subresource query
   *
   * @return object The subresource object
   */
  public function fetch_sub_resource($type, $params = array()) {

    // Generate subresource endpoint by snipping out the
    // right part of the sub_resource_uri
    $endpoint = preg_replace('/api\/v[0-9]+\//', '',
      parse_url($this->sub_resource_uris[$type], PHP_URL_PATH));

    $sub_resource_params = array();

    // Extract params from subresource uri if available and create array
    if ($param_string = parse_url($this->sub_resource_uris[$type],
      PHP_URL_QUERY)) {

      $split_params = explode('&', $param_string);

      foreach ($split_params as $split_param) {
          $parts = explode('=', $split_param);
          $sub_resource_params[$parts[0]] = $parts[1];
      }

    }

    $params = array_merge($params, $sub_resource_params);

    $class = 'GoCardless_' .
      GoCardless_Utils::camelize(GoCardless_Utils::singularize($type));

    $objects = array();

    foreach ($this->client->request('get', $endpoint, $params) as $value) {
      $objects[] = new $class($this->client, $value);
    }

    return $objects;

  }

}
