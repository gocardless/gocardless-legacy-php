<?php

/**
 * GoCardless bill functions
 *
 */

/**
 * GoCardless bill class
 *
 */
class GoCardless_Bill {
  
  public static $endpoint = '/bills';
  
  function __construct($client, $attrs) {
    
    $this->client = $client;
    
    foreach ($attrs as $key => $value) {
      $this->$key = $value;
    }
    
  }
  
  /**
   * Fetch a bill item from the API
   *
   * @param string $id The id of the bill to fetch
   *
   * @return object The bill object
   */
  public static function find($id, $client = null) {
    
    $endpoint = self::$endpoint . '/' . $id;
    
    return new self(GoCardless::$client, GoCardless::$client->apiGet($endpoint));
    
  }
  
  /**
   * Fetch a bill item from the API
   *
   * @param string $id The id of the bill to fetch
   *
   * @return object The bill object
   */
  public function findWithClient($client, $id) {
    
    $endpoint = self::$endpoint . '/' . $id;
    
    return new self($client, GoCardless::$client->apiGet($endpoint));
    
  }
  
  /**
   * Create a bill under an existing pre-auth
   *
   * @param string $id The pre-auth
   *
   * @return object The result of the cancel query
   */
  public function create($params) {
    
    $params['headers']['authorization'] = true;
    
    return new self($this->$client, $this->client->apiPost(self::$endpoint, $params));
    
  }
  
}

?>