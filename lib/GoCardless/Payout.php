<?php

/**
 * GoCardless payout functions
 *
 * @package GoCardless\Payout
 */

/**
 * GoCardless payout class
 *
 */
class GoCardless_Payout extends GoCardless_Resource {

  /**
   * The API endpoint for payouts
   *
   * @var string $endpoint
   */
  public static $endpoint = '/payouts';

  /**
   * Instantiate a new instance of the payout object
   *
   * @param object $client The client to use for the payout object
   * @param array $attrs The properties of the payout
   *
   * @return object The payout object
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
   * Fetch a payout item from the API
   *
   * @param string $id The id of the payout to fetch
   * @param object $client The client object to use to make the query
   *
   * @return object The payout object
   */
  public static function find($id, $client = null) {

    $endpoint = self::$endpoint . '/' . $id;

    if ($client == null) {
      $client = GoCardless::$client;
    }

    return new self($client, $client->request('get', $endpoint));

  }

  /**
   * Fetch a payout item from the API
   *
   * @param object $client The client object to use to make the query
   * @param string $id The id of the payout to fetch
   *
   * @return object The payout object
   */
  public static function find_with_client($client, $id) {

    $endpoint = self::$endpoint . '/' . $id;

    return new self($client, $client->request('get', $endpoint));

  }

}
