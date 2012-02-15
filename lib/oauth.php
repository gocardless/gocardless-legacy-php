<?php

/**
 * GoCardless oauth functions
 *
 */

/**
 * GoCardless oauth class
 *
 */
class OAuth {
  
  /**
   * Generate an OAuth authorization URL
   *
   * @return string The url to send the user to
   */
  public static function authorizeUrl($params) {
    
    $endpoint = '/oauth/authorize';
    
    $url =  GoCardless_Client::$base_url . $endpoint .
        '?client_id='. urlencode($params['client_id']) .
        '&redirect_uri=' . urlencode($params['redirect_uri']) .
        '&scope=manage_merchant' .
        '&response_type=code';
    
    return $url;
    
  }
  
  /**
   * Fetch an access token given the code returned in the first
   * part of the OAuth process
   *
   * @param array $params Parameters to make the request
   *
   * @return string The API response including the access token
   */
  public static function getToken($params) {
    
    $url = '/oauth/access_token';
    $params['headers']['http_authorization'] = true;
    
    $access_token = GoCardless_Client::apiPost($url, $params);
    
    return $access_token;
    
  }
  
}

?>