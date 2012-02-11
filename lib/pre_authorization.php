<?php

class GoCardless_Pre_Authorization {
	
	public function get($id) {
		$endpoint = 'pre_authorizations/' . $id;
		return Utils::fetch_resource($endpoint);
	}
	
	public function cancel($id) {
		$endpoint = 'pre_authorizations/' . $id . '/cancel';
		return Utils::fetch_resource($endpoint);
	}
	
}

?>