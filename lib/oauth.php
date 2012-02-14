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
	public static function authorizeUrl() {
		
		$endpoint = '/oauth/authorize';
		
		$client_id = urlencode('EuHqvzOJfD9NFSACSK8Q0ZfpwpmbyQao4NdYbgi0IidwlQQ_HzIgdrVZsjRUosNc');
		$redirect_uri = urlencode('http://localhost:8888/demo_partner.php');
		
		$url = Client::$base_url . $endpoint . '?client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=manage_merchant';
		
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
	public static function fetchAccessToken($params) {
		
		$url = '/oauth/access_token';
		$params['headers']['http_authorization'] = true;
		
		$access_token = Client::apiPost($url, $params);
		
		return $access_token;
		
	}
	
}

?>