<?php

class Pre_Authorization {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function get($id) {
		$endpoint = 'pre_authorizations/' . $id;
		return $this->gocardless->fetch_resource($endpoing);
	}
	
	public function cancel($id) {
		$endpoint = 'pre_authorizations/' . $id . '/cancel';
		return $this->gocardless->fetch_resource($endpoint);
	}
	
}

?>