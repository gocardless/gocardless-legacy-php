<?php

/**
 * GoCardless user functions
 *
 */

/**
 * GoCardless user class
 *
 */
class GoCardless_User {

  public static $endpoint = '/users';

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
