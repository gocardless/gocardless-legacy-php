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
  function __construct($client, $attrs) {

    $this->client = $client;

    foreach ($attrs as $key => $value) {
      $this->$key = $value;
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

    $endpoint = self::$endpoint . '/' . $id;

    return new self(GoCardless::$client, GoCardless_Request::get($endpoint));

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

    $endpoint = self::$endpoint . '/' . $id;

    return new self($client, GoCardless_Request::get($endpoint));

  }

  /**
   * Fetch a merchant's subscriptions from the API
   *
   * @return array Array of subscription objects
   */
  public function subscriptions() {

    $objects = array();

    $endpoint = self::$endpoint . '/' . $this->id . '/subscriptions';

    foreach (GoCardless_Request::get($endpoint) as $value) {
      $objects[] = new GoCardless_Subscriptions($this->client, $value);
    }

    return $objects;

  }

  /**
   * Fetch a merchant's pre-authorisations from the API
   *
   * @return array Array of pre-authorisation objects
   */
  public function pre_authorizations() {

    $endpoint = self::$endpoint . '/' . $this->id . '/pre_authorizations';

    $objects = array();

    foreach (GoCardless_Request::get($endpoint) as $value) {
      $objects[] = new GoCardless_PreAuthorization($this->client, $value);
    }

    return $objects;

  }

  /**
   * Fetch a list of the users associated with a given merchant
   *
   * @return array Array of user objects
   */
  public function users() {

    $endpoint = self::$endpoint . '/' . $this->id . '/users';

    $objects = array();

    foreach (GoCardless_Request::get($endpoint) as $value) {
      $objects[] = new GoCardless_Users($this->client, $value);
    }

    return $objects;

  }

  /**
   * Fetch a merchant's bills from the API
   *
   * @return array Array of bill objects
   */
  public function bills() {

    $endpoint = self::$endpoint . '/' . $this->id . '/bills';

    $objects = array();

    foreach (GoCardless_Request::get($endpoint) as $value) {
      $objects[] = new GoCardless_Bill($this->client, $value);
    }

    return $objects;

  }

}

?>
