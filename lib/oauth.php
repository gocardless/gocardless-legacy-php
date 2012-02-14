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
	 * Fetch a bill item from the API
	 *
	 * @param string $id The id of the bill to fetch
	 *
	 * @return object The bill object
	 */
	public static function authorizeUrl($params) {
		
		$endpoint = '/oauth/authorize';
		
		$client_id = urlencode('EuHqvzOJfD9NFSACSK8Q0ZfpwpmbyQao4NdYbgi0IidwlQQ_HzIgdrVZsjRUosNc');
		$redirect_uri = urlencode('http://localhost:8888/demo_partner.php');
		
		$url = Client::$base_url . $endpoint . '?client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=manage_merchant';
		
		return $url;
		
	}
	
	/**
	 * Send in the code and return the access token
	 *
	 * @param array $params Parameters to make the request
	 *
	 * @return string The returned string including the access token
	 */
	public static function fetchAccessToken($params) {
		
		$url = '/oauth/access_token';
		$params['headers']['http_authorization'] = true;
		
		$access_token = Client::apiPost($url, $params);
		
		return $access_token;
		
	}
	
}

?>