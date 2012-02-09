<?php

class Merchant {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function get($id) {
		$endpoint = 'merchants/' . $id;
		return $this->gocardless->fetch_resource($endpoint);
	}
	
	public function subscriptions($id) {
		$endpoint = 'merchants/' . $id . '/subscriptions';
		return $this->gocardless->fetch_resource($endpoint);
	}
	
	public function pre_authorizations($id) {
		$endpoint = 'merchants/ ' . $id . '/pre_authorizations';
		return $this->gocardless->fetch_resource($endpoint);
	}
	
	public function users($id) {
		$endpoint = 'merchants/' . $id . '/users';
		return $this->gocardless->fetch_resource($endpoint);
	}
	
	public function bills($id) {
		$endpoint = 'merchants/' . $id . '/bills';
		return $this->gocardless->fetch_resource($endpoint);
	}
	
	public function payments($id) {
		$endpoint = 'merchants/' . $id . '/payments';
		return $this->gocardless->fetch_resource($endpoint);
	}
	
}

?>