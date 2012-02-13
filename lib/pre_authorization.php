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
	
	/**
	 * Fetch a pre-authorisation item from the API
	 *
	 * @param string $id The id of the pre-authorisation to fetch
	 *
	 * @return object The pre-authorisations object
	 */
	public static function find($id) {
		$endpoint = '/pre_authorizations/' . $id;
		return Utils::fetchResource($endpoint);
	}
	
	/**
	 * Cancel a pre-authorisation
	 *
	 * @param string $id The id of the pre-authorisation to fetch
	 *
	 * @return object The result of the cancel query
	 */
	public static function cancel($id) {
		$endpoint = '/pre_authorizations/' . $id . '/cancel';
		return Utils::fetchResource($endpoint);
	}
	
}

?>