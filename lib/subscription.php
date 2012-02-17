<?php

/**
 * GoCardless subscription functions
 *
 */

/**
 * GoCardless subscription class
 *
 */
class GoCardless_Subscription {

  public static $endpoint = '/subscriptions';

  function __construct($client, $attrs) {

    $this->client = $client;

    foreach ($attrs as $key => $value) {
      $this->$key = $value;
    }

  }

  /**
   * Fetch a subscription item from the API
   *
   * @param string $id The id of the subscription to fetch
   *
   * @return object The subscription object
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

  /**
   * Cancel a subscription in the API
   *
   * @param string $id The id of the subscription to fetch
   *
   * @return object The result of the cancel query
   */
  public function cancel() {

    $endpoint = self::$endpoint . '/' . $this->id . '/cancel';

    return new self($this->client, $this->client->api_put($endpoint));

  }

}

?>
