<?php

/**
 * GoCardless subscription functions
 *
 */

/**
 * GoCardless subscription class
 *
 */
class GoCardless_Subscription {
	
	function __construct($id) {
		
		$merchant = self::find($id);
		
		foreach ($merchant as $key => $value) {
			$this->$key = $value;
		}
		
	}
	
	/**
	 * Fetch a subscription item from the API
	 *
	 * @param string $id The id of the subscription to fetch
	 *
	 * @return object The subscription object
	 */
	public function find($id) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/subscriptions/' . $id;
		return Utils::fetchResource($endpoint);
		
	}
	
	/**
	 * Cancel a subscription in the API
	 *
	 * @param string $id The id of the subscription to fetch
	 *
	 * @return object The result of the cancel query
	 */
	public function cancel($id) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/subscriptions/' . $id . '/cancel';
		return Utils::fetchResource($endpoint);
		
	}
	
}

?>