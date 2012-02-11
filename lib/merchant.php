<?php

class GoCardless_Merchant {
	
	public function find($id) {
		$endpoint = 'merchants/' . $id;
		return Utils::fetch_resource($path);
	}
	
	public function subscriptions($id) {
		$endpoint = 'merchants/' . $id . '/subscriptions';
		return Utils::fetch_resource($path);
	}
	
	public function pre_authorizations($id) {
		$endpoint = 'merchants/ ' . $id . '/pre_authorizations';
		return Utils::fetch_resource($endpoint);
	}
	
	public function users($id) {
		$endpoint = 'merchants/' . $id . '/users';
		return Utils::fetch_resource($endpoint);
	}
	
	public function bills($id) {
		$endpoint = 'merchants/' . $id . '/bills';
		return Utils::fetch_resource($endpoint);
	}
	
	public function payments($id) {
		$endpoint = 'merchants/' . $id . '/payments';
		return Utils::fetch_resource($endpoint);
	}
	
}

?>