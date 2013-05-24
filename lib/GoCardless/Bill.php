<?php

/**
 * GoCardless bill functions
 *
 * @package GoCardless\Bill
 */

/**
 * GoCardless bill class
 *
 */
class GoCardless_Bill extends GoCardless_Resource {

  /**
   * The API endpoint for bills
   *
   * @var string $endpoint
   */
  public static $endpoint = '/bills';

  /**
   * Instantiate a new instance of the bill object
   *
   * @param object $client The client to use for the bill object
   * @param array $attrs The properties of the bill
   *
   * @return object The bill object
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
   * Fetch a bill item from the API
   *
   * @param string $id The id of the bill to fetch
   * @param object $client The client object to use to make the query
   *
   * @return object The bill object
   */
  public static function find($id, $client = null) {

    $endpoint = self::$endpoint . '/' . $id;

    if ($client == null) {
      $client = GoCardless::$client;
    }

    return new self($client, $client->request('get', $endpoint));

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
   * Attempt to collect a bill with status 'failed' again
   *
   * @return object The result of the retry query
   */
  public function retry() {

    $endpoint = self::$endpoint . '/' . $this->id . '/retry';

    return new self($this->client, $this->client->request('post', $endpoint));

  }

  /**
   * Fetch the payout for a bill, if a payout_id is recorded
   * @return object A GoCardless_Payout object representing the payout
   */
  public function payout() {
    if (!$this->payout_id) { throw new GoCardless_ClientException("Cannot fetch payout for a bill that has not been paid out"); }
    return GoCardless_Payout::find_with_client($this->client, $this->payout_id);
  }

}
