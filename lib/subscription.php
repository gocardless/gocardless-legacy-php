<?php

/**
 * GoCardless subscription class
 *
 */
class GoCardless_Subscription {
	
	/**
	 * Fetch a subscription item from the API
	 *
	 * @param string $id The id of the subscription to fetch
	 *
	 * @return object The subscription object
	 */
	public static function find($id) {
		$endpoint = '/subscriptions/' . $id;
		return Utils::fetch_resource($endpoint);
	}
	
	/**
	 * Cancel a subscription in the API
	 *
	 * @param string $id The id of the subscription to fetch
	 *
	 * @return object The result of the cancel query
	 */
	public function cancel($id) {
		$endpoint = '/subscriptions/' . $id . '/cancel';
		return Utils::fetch_resource($endpoint);
	}
	
}

?>