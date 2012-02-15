<?php

/**
 * GoCardless pre-authorisation functions
 *
 */

/**
 * GoCardless pre-authorization class
 *
 */
class GoCardless_Pre_Authorization {
  
  function __construct($id) {
    
    $pre_auth = self::find($id);
    
    foreach ($pre_auth as $key => $value) {
      $this->$key = $value;
    }
    
  }
  
  /**
   * Fetch a pre-authorisation item from the API
   *
   * @param string $id The id of the pre-authorisation to fetch
   *
   * @return object The pre-authorisations object
   */
  public function find($id = null) {

    if ($id == null) {
      $id = $this->id;
    }
    
    $endpoint = '/pre_authorizations/' . $id;
    return Utils::fetchResource($endpoint);
    
  }
  
  /**
   * Fetch a pre-authorisation item from the API
   *
   * @param string $id The id of the pre-authorisation to fetch
   *
   * @return object The pre-authorisations object
   */
  public function createBill($params = null) {
    
    return GoCardless_Bill::create($params);
    
  }
  
  /**
   * Cancel a pre-authorisation
   *
   * @param string $id The id of the pre-authorisation to fetch
   *
   * @return object The result of the cancel query
   */
  public function cancel($id = null) {
    
    if ($id == null) {
      $id = $this->id;
    }
    
    $endpoint = '/pre_authorizations/' . $id . '/cancel';
    return Utils::fetchResource($endpoint);
    
  }
  
}

?>