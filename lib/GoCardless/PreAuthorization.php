<?php

/**
 * GoCardless pre-authorisation functions
 *
 * @package GoCardless\PreAuthorization
 */

/**
 * GoCardless pre-authorization class
 *
 */
class GoCardless_PreAuthorization {

  /**
   * The API endpoint for pre-authorizations
   *
   * @var string $endpoint
   */
  public static $endpoint = '/pre_authorizations';

  /**
   * Instantiate a new instance of the pre-auth object
   *
   * @param object $client The client to use for the pre-auth object
   * @param array $attrs The properties of the pre-auth
   *
   * @return object The pre-auth object
   */
  function __construct($client, $attrs) {

    $this->client = $client;

    foreach ($attrs as $key => $value) {
      $this->$key = $value;
    }

  }

  /**
   * Fetch a pre-authorisation object from the API
   *
   * @param string $id The id of the pre-authorisation to fetch
   *
   * @return object The pre-authorisation object
   */
  public static function find($id) {

    $endpoint = self::$endpoint . '/' . $id;
    $params['http_bearer'] = GoCardless::$client->account_details['access_token'];

    return new self(GoCardless::$client, GoCardless_Request::get($endpoint, $params));

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
    $params['http_bearer'] = GoCardless::$client->account_details['access_token'];

    return new self($client, GoCardless_Request::get($endpoint, $params));

  }

  /**
   * Create a bill under an existing pre-authorization
   *
   * @param array $attrs The properties of the bill being created
   *
   * @return object The pre-authorisations object
   */
  public function create_bill($attrs) {

    if(!isset($attrs['amount'])) {
      throw new GoCardless_ArgumentsException('Amount required');
    }

    $params = array(
      'bill' => array(
        'amount'                => $attrs['amount'],
        'pre_authorization_id'  => $this->id
      )
    );

    if (isset($attrs['name'])) {
      $params['bill']['name'] = $attrs['name'];
    }

    if (isset($attrs['description'])) {
      $params['bill']['description'] = $attrs['description'];
    }

    $endpoint = GoCardless_Bill::$endpoint;
    $params['http_bearer'] = $this->client->account_details['access_token'];

    return new GoCardless_Bill($this->client, GoCardless_Request::post($endpoint, $params));

  }

  /**
   * Cancel a pre-authorisation
   *
   * @return object The result of the cancel query
   */
  public function cancel() {

    $endpoint = self::$endpoint . '/' . $this->id . '/cancel';
    $params['http_bearer'] = $this->client->account_details['access_token'];

    return new self($this->client, GoCardless_Request::put($endpoint));

  }

}
