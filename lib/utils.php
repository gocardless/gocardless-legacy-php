<?php

/**
 * GoCardless utility functions
 *
 */

/**
 * GoCardless utils class
 *
 */
class Utils {
	
	/**
	 * Generate mandatory payment parameters: client_id, nonce and timestamp
	 *
	 * @return array Mandatory payment parameters
	 */
	public static function generate_mandatory_params() {
		
		// Create new UTC date object
		$date = new DateTime(null, new DateTimeZone('UTC'));
		
		$request = array(
			'client_id'	=> GoCardless::$account_details['app_id'],
			'nonce'		=> Client::generate_nonce(),
			'timestamp'	=> $date->format('Y-m-d\TH:i:s\Z')
		);
		
		return $request;
		
	}
	
	/**
	 * Generate a signature for a request given the app secret
	 *
	 * @param array $params The parameters to generate a signature for
	 * @param string $key The key to generate the signature with
	 *
	 * @return string A URL-encoded string of parameters
	 */
	public static function generate_signature($params, $key) {
		
		return hash_hmac('sha256', Utils::generate_query_string($params), $key);
		
	}
	
	/**
	 * Generates, encodes, re-orders variables for the query string.
	 *
	 * @param array $params The specific parameters for this payment
	 * @param array $pairs Pairs
	 * @param string $namespace The namespace
	 *
	 * @return string An encoded string of parameters
	 */
	public static function generate_query_string($params, &$pairs = array(), $namespace = null) {
		
		if (is_array($params)) {
			
			foreach ($params as $k => $v) {
				
				if (is_int($k)) {
					Utils::generate_query_string($v, $pairs, $namespace . '[]');
				} else {
					Utils::generate_query_string($v, $pairs, $namespace !== null ? $namespace . "[$k]" : $k);
				}
				
			}
			
			if ($namespace !== null) {
				return $pairs;
			}
	
			if (empty($pairs)) {
				return '';
			}
			
			sort($pairs);
			$strs = array_map('implode', array_fill(0, count($pairs), '='), $pairs);
			
			return implode('&', $strs);
			
		} else {
			
			$pairs[] = array(rawurlencode($namespace), rawurlencode($params));
			
		}
		
	}
	
	/**
	 * Fetches a resource from an API endpoint
	 *
	 * @param string $endpoint The endpoint to send the request to
	 * @param string $method The method to use to send the request
	 *
	 * @return object The returned resource
	 */
	public static function fetch_resource($endpoint, $method = 'get') {
		
		$params = array();
		
		if (isset(GoCardless::$account_details['access_token'])) {
			
			// Add Authorization header
			//$auth_header = 'Authorization: Bearer ' . GoCardless::$account_details['access_token'];
			//$params['headers'][CURLOPT_HTTPHEADER] = $auth_header;
			
			$params['headers']['authorization'] = true;
			
			//if (!array_search($auth_header, $curl_options[CURLOPT_HTTPHEADER])) {
			//	$curl_options[CURLOPT_HTTPHEADER][] = $auth_header;
			//}
			
		} else {
			
			throw new GoCardlessClientException('Access token missing');
			
		}
		
		// Do query
		if ($method == 'get') {
			$result = Client::api_get(Client::$api_path . $endpoint, $params);
		} else {
			$result = Client::api_post(Client::$api_path . $endpoint, $params);
		}
		
		$object = json_decode($result);
		
		return $object;
		
	}
	
}

?>