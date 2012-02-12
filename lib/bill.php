<?php

class GoCardless_Bill {
	
	/**
	 * Fetch a bill item from the API
	 *
	 * @param string $id The id of the bill to fetch
	 *
	 * @return object The bill object
	 */
	public function find($id) {
		$endpoint = '/bills/' . $id;
		return Utils::fetch_resource($endpoint);
	}
	
	/**
	 * Cancel a bill using the API
	 *
	 * @param string $id The id of the bill to cancel
	 *
	 * @return object The result of the cancel query
	 */
	public function cancel($id) {
		$endpoint = '/bills/' . $id . '/cancel';
		return Utils::fetch_resource($endpoint);
	}
	
}

?>