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
   * Fetch a merchant's subscriptions from the API
   *
   * @param array $params Params to append to the query ie. for filtering
   *
   * @return array Array of subscription objects
   */
  public function subscriptions($params = array()) {

    $objects = array();

    $endpoint = self::$endpoint . '/' . $this->id . '/subscriptions';

    foreach ($this->client->request('get', $endpoint, $params) as $value) {
      $objects[] = new GoCardless_Subscription($this->client, $value);
    }

    return $objects;
  }

  /**
   * Fetch a merchant's pre-authorisations from the API
   *
   * @param array $params Params to append to the query ie. for filtering
   *
   * @return array Array of pre-authorisation objects
   */
  public function pre_authorizations($params = array()) {

    $endpoint = self::$endpoint . '/' . $this->id . '/pre_authorizations';

    $objects = array();

    foreach ($this->client->request('get', $endpoint, $params) as $value) {
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

    foreach ($this->client->request('get', $endpoint) as $value) {
      $objects[] = new GoCardless_User($this->client, $value);
    }

    return $objects;

  }

  /**
   * Fetch a merchant's bills from the API
   *
   * @param array $params Params to append to the query ie. for filtering
   *
   * @return array Array of bill objects
   */
  public function bills($params = array()) {

    $endpoint = self::$endpoint . '/' . $this->id . '/bills';

    $objects = array();

    foreach ($this->client->request('get', $endpoint, $params) as $value) {
      $objects[] = new GoCardless_Bill($this->client, $value);
    }

    return $objects;

  }

}
