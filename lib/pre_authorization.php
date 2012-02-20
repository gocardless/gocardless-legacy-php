<?php

/**
 * GoCardless pre-authorisation functions
 *
 */

/**
 * GoCardless pre-authorization class
 *
 */
class GoCardless_PreAuthorization {

  public static $endpoint = '/pre_authorizations';

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
   * Fetch a pre-authorisation item from the API
   *
   * @param string $id The id of the pre-authorisation to fetch
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

    return new GoCardless_Bill($this->client, $this->client->api_post($endpoint, $params));

  }

  /**
   * Cancel a pre-authorisation
   *
   * @param string $id The id of the pre-authorisation to fetch
   *
   * @return object The result of the cancel query
   */
  public function cancel() {

    $endpoint = self::$endpoint . '/' . $this->id . '/cancel';

    return new self($this->client, $this->client->api_put($endpoint));

  }

}

?>
