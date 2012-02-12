<?php

/**
 * GoCardless merchant class
 *
 */
class GoCardless_Merchant {
	
	/**
	 * Fetch a merchant from the API
	 *
	 * @param string $id The id of the merchant to fetch
	 *
	 * @return object The merchant object
	 */
	public static function find($id) {
		$endpoint = '/merchants/' . $id;
		return Utils::fetch_resource($endpoint);
	}
	
	/**
	 * Fetch a merchant's subscriptions from the API
	 *
	 * @param string $id The id of the merchant's subscriptions to fetch
	 *
	 * @return array Array of subscription objects
	 */
	public function subscriptions($id) {
		$endpoint = '/merchants/' . $id . '/subscriptions';
		return Utils::fetch_resource($endpoint);
	}
	
	/**
	 * Fetch a merchant's pre-authorisations from the API
	 *
	 * @param string $id The id of the merchant's pre-authorisations to fetch
	 *
	 * @return array Array of pre-authorisation objects
	 */
	public function pre_authorizations($id) {
		$endpoint = '/merchants/ ' . $id . '/pre_authorizations';
		return Utils::fetch_resource($endpoint);
	}
	
	/**
	 * Fetch a list of the users associated with a given merchant
	 *
	 * @param string $id The id of the merchant's users to fetch
	 *
	 * @return array Array of user objects
	 */
	public function users($id) {
		$endpoint = '/merchants/' . $id . '/users';
		return Utils::fetch_resource($endpoint);
	}
	
	/**
	 * Fetch a merchant's bills from the API
	 *
	 * @param string $id The id of the merchant's bills to fetch
	 *
	 * @return array Array of bill objects
	 */
	public function bills($id) {
		$endpoint = '/merchants/' . $id . '/bills';
		return Utils::fetch_resource($endpoint);
	}
	
	/**
	 * Fetch a merchant's payments from the API
	 *
	 * @param string $id The id of the merchant's payments to fetch
	 *
	 * @return array Array of payment objects
	 */
	public function payments($id) {
		$endpoint = '/merchants/' . $id . '/payments';
		return Utils::fetch_resource($endpoint);
	}
	
}

?>