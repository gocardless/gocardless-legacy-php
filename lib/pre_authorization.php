<?php

class Pre_Authorization {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function get($id) {
		return $this->gocardless->fetch_resource('pre_authorizations/' . $id);
	}
	
	public function cancel($id) {
		return $this->gocardless->fetch_resource('pre_authorizations/' . $id . '/cancel');
	}
	
}

?>