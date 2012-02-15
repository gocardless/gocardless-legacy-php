<?php

/**
 * GoCardless payment functions
 *
 */

/**
 * GoCardless payment class
 *
 */
class GoCardless_Payment {
  
  function __construct($id) {
    
    $payment = self::find($id);
    
    foreach ($payment as $key => $value) {
      $this->$key = $value;
    }
    
  }
  
  /**
   * Fetch a payment item from the API
   *
   * @param string $id The id of the payment to fetch
   *
   * @return object The payment object
   */
  public function find($id) {
    
    if ($id == null) {
      $id = $this->id;
    }
    
    $endpoint = '/payments/' . $id;
    return Utils::fetchResource($endpoint);
    
  }
  
}

?>