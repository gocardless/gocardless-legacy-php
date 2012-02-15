<?php

/**
 * GoCardless bill functions
 *
 */

/**
 * GoCardless bill class
 *
 */
class GoCardless_Bill {
	
	function __construct($id) {
		
		$bill = self::find($id);
		
		foreach ($bill as $key => $value) {
			$this->$key = $value;
		}
		
	}
	
	/**
	 * Fetch a bill item from the API
	 *
	 * @param string $id The id of the bill to fetch
	 *
	 * @return object The bill object
	 */
	public function find($id) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/bills/' . $id;
		return Utils::fetchResource($endpoint);
		
	}
	
	/**
	 * Create a bill under an existing pre-auth
	 *
	 * @param string $id The pre-auth
	 *
	 * @return object The result of the cancel query
	 */
	public function create($params) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/bills';
		return GoCardless_Client::apiPost(GoCardless_Client::$api_path . $endpoint, $params);
		
	}
	
	/**
	 * Cancel a bill using the API
	 *
	 * @param string $id The id of the bill to cancel
	 *
	 * @return object The result of the cancel query
	 */
	public function cancel($id) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/bills/' . $id . '/cancel';
		return Utils::fetchResource($endpoint);
		
	}
	
}

?>