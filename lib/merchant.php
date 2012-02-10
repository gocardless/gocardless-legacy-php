<?php

class GoCardless__Merchant extends Resource {
	
	public static function get($id) {
		$a = GoCardless__Merchant::find($id, 'merchants/' . $id);
	}
	
	public function subscriptions($id) {
		$path = 'merchants/' . $id . '/subscriptions';
		return $this->gocardless->fetch_resource($path);
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