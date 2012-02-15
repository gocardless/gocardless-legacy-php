<?php

/**
 * GoCardless merchant functions
 *
 */

/**
 * GoCardless merchant class
 *
 */
class GoCardless_Merchant {
	
	function __construct($id) {
		
		$merchant = self::find($id);
		
		foreach ($merchant as $key => $value) {
			$this->$key = $value;
		}
		
	}
	
	/**
	 * Fetch a merchant from the API
	 *
	 * @param string $id The id of the merchant to fetch
	 *
	 * @return object The merchant object
	 */
	public function find($id = null) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/merchants/' . $id;
		return Utils::fetchResource($endpoint);
		
	}
	
	/**
	 * Fetch a merchant's subscriptions from the API
	 *
	 * @param string $id The id of the merchant's subscriptions to fetch
	 *
	 * @return array Array of subscription objects
	 */
	public function subscriptions($id = null) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/merchants/' . $id . '/subscriptions';
		return Utils::fetchResource($endpoint);
		
	}
	
	/**
	 * Fetch a merchant's pre-authorisations from the API
	 *
	 * @param string $id The id of the merchant's pre-authorisations to fetch
	 *
	 * @return array Array of pre-authorisation objects
	 */
	public function pre_authorizations($id) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/merchants/ ' . $id . '/pre_authorizations';
		return Utils::fetchResource($endpoint);
		
	}
	
	/**
	 * Fetch a list of the users associated with a given merchant
	 *
	 * @param string $id The id of the merchant's users to fetch
	 *
	 * @return array Array of user objects
	 */
	public function users($id) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/merchants/' . $id . '/users';
		return Utils::fetchResource($endpoint);
		
	}
	
	/**
	 * Fetch a merchant's bills from the API
	 *
	 * @param string $id The id of the merchant's bills to fetch
	 *
	 * @return array Array of bill objects
	 */
	public function bills($id) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/merchants/' . $id . '/bills';
		return Utils::fetchResource($endpoint);
		
	}
	
	/**
	 * Fetch a merchant's payments from the API
	 *
	 * @param string $id The id of the merchant's payments to fetch
	 *
	 * @return array Array of payment objects
	 */
	public function payments($id) {
		
		if ($id == null) {
			$id = $this->id;
		}
		
		$endpoint = '/merchants/' . $id . '/payments';
		return Utils::fetchResource($endpoint);
		
	}
	
}

?>