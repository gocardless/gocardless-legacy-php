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

    // Check for subresources
    if (isset($this->sub_resource_uris)) {

      // Loop through each subresource loading it as appropriate object
      foreach ($this->sub_resource_uris as $key => $value) {

        // Generate subresource endpoint by snipping out the
        // right part of the url
        $endpoint = preg_replace('/api\/v[0-9]+\//', '',
          parse_url($value, PHP_URL_PATH));

        // Add params to endpoint
        if (parse_url($value, PHP_URL_QUERY)) {
          $endpoint .= '/?' . parse_url($value, PHP_URL_QUERY);
        }

        // Generate the class name
        $class = 'GoCardless_' .
          GoCardless_Utils::camelize(GoCardless_Utils::singularize($key));

        // Create an array for the subresource
        $this->$key = array();

        // Query the API
        foreach ($this->client->request('get', $endpoint) as $value) {

          // Load each element into the appropriate class
          $this->{$key}[] = new $class($this->client, $value);

        }

      }

      // Unset the sub_resource_uris feild as we now have now loaded the
      // subresources themselves
      unset($this->sub_resource_uris);

    }

  }

  /**
   * This magic method is used to call subresources ie. merchant()->users()
   *
   * @param string $method The name of the method being called
   *
   * @return array The subresource index
   */
  public function __call($method, $params) {

    // Check the subresource exists
    if (isset($this->$method)) {

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

    $endpoint = self::$endpoint . '/' . $this->id . '/' . $type;

    $class = 'GoCardless_' .
      GoCardless_Utils::camelize(GoCardless_Utils::singularize($type));

    $objects = array();

    foreach ($this->client->request('get', $endpoint, $params) as $value) {
      $objects[] = new $class($this->client, $value);
    }

    return $objects;

  }

}
