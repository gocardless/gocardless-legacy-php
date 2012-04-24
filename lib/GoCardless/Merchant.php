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
class GoCardless_Merchant extends GoCardless_Resource {

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

}
