<?php

/**
 * GoCardless resource functions
 *
 * @package GoCardless\Resource
 */

/**
 * GoCardless resource class
 *
 */
class GoCardless_Resource {

  /**
   * This magic method is used to call subresources
   *
   * @param string $method The name of the method being called
   * @param array $arguments The arguments to pass to the method
   *
   * @return array The subresource index
   */
  public function __call($method, $arguments = array()) {

    // Params may be passed as first argument to method
    // If not, use empty array
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

    // Extract params from subresource uri if available and create
    // sub_resource_params array
    if ($param_string = parse_url($this->sub_resource_uris[$type],
      PHP_URL_QUERY)) {

      $split_params = explode('&', $param_string);

      foreach ($split_params as $split_param) {
          $parts = explode('=', $split_param);
          $sub_resource_params[$parts[0]] = $parts[1];
      }

    }

    // Overwrite params from subresource uri with passed params, if found
    $params = array_merge($params, $sub_resource_params);

    // Get class name
    $class = 'GoCardless_' .
      GoCardless_Utils::camelize(GoCardless_Utils::singularize($type));

    $objects = array();

    // Create an array of objects
    foreach ($this->client->request('get', $endpoint, $params) as $value) {
      $objects[] = new $class($this->client, $value);
    }

    // Return the array of objects
    return $objects;

  }

}
