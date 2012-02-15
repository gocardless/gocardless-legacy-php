<?php

/**
 * GoCardless user functions
 *
 */

/**
 * GoCardless user class
 *
 */
class GoCardless_User {
  
  function __construct($id) {
    
    $user = self::find($id);
    
    foreach ($user as $key => $value) {
      $this->$key = $value;
    }
    
  }
  
  /**
   * Fetch a user item from the API
   *
   * @param string $id The id of the user to fetch
   *
   * @return object The user object
   */
  public function find($id = null) {
    
    if ($id == null) {
      $id = $this->id;
    }
    
    $endpoint = '/users/' . $id;
    return Utils::fetchResource($endpoint);
    
  }
  
}

?>