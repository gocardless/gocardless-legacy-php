<?php

/**
 * GoCardless user functions
 *
 * @package GoCardless\User
 */

/**
 * GoCardless user class
 *
 */
class GoCardless_User {

  /**
   * The API endpoint for users
   *
   * @var string $endpoint
   */
  public static $endpoint = '/users';

  /**
   * Instantiate a new instance of the user object
   *
   * @param object $client The client to use for the user object
   * @param array $attrs The properties of the user
   *
   * @return object The user object
   */
  function __construct($client, $attrs) {

    $this->client = $client;

    foreach ($attrs as $key => $value) {
      $this->$key = $value;
    }

  }

  /**
   * Fetch a user item from the API
   *
   * @param string $id The id of the user to fetch
   *
   * @return object The user object
   */
  public static function find($id) {

    $endpoint = self::$endpoint . '/' . $id;

    return new self(GoCardless::$client, GoCardless::$client->api_get($endpoint));

  }

  /**
   * Fetch a bill item from the API
   *
   * @param object $client The client object to use to make the query
   * @param string $id The id of the bill to fetch
   *
   * @return object The bill object
   */
  public static function find_with_client($client, $id) {

    $endpoint = self::$endpoint . '/' . $id;

    return new self($client, $client->api_get($endpoint));

  }

}

?>
