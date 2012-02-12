<?php

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
	public function find($id) {
		$endpoint = '/pre_authorizations/' . $id;
		return Utils::fetch_resource($endpoint);
	}
	
	/**
	 * Cancel a pre-authorisation
	 *
	 * @param string $id The id of the pre-authorisation to fetch
	 *
	 * @return object The result of the cancel query
	 */
	public function cancel($id) {
		$endpoint = '/pre_authorizations/' . $id . '/cancel';
		return Utils::fetch_resource($endpoint);
	}
	
}

?>