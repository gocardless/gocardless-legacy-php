<?php

/**
 * GoCardless subscription functions
 *
 * @package GoCardless\Subscription
 */

/**
 * GoCardless subscription class
 *
 */
class GoCardless_Subscription {

  /**
   * The API endpoint for subscriptions
   *
   * @var string $endpoint
   */
  public static $endpoint = '/subscriptions';

  /**
   * Instantiate a new instance of the subscription object
   *
   * @param object $client The client to use for the subscription object
   * @param array $attrs The properties of the subscription
   *
   * @return object The subscription object
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
   * Fetch a subscription item from the API
   *
   * @param string $id The id of the subscription to fetch
   *
   * @return object The subscription object
   */
  public static function find($id) {

    $endpoint = self::$endpoint . '/' . $id;

    return new self(GoCardless::$client, GoCardless::$client->request('get', $endpoint));

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

    return new self($client, $client->request('get', $endpoint));

  }

  /**
   * Cancel a subscription in the API
   *
   * @return object The result of the cancel query
   */
  public function cancel() {

    $endpoint = self::$endpoint . '/' . $this->id . '/cancel';

    return new self($this->client, $this->client->request('put', $endpoint));

  }

}
