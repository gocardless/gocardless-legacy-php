<?php

class Bill {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function get($id) {
		return $this->gocardless->fetch_resource('bills/' . $id);
	}
	
	public function cancel($id) {
		return $this->gocardless->fetch_resource('bills/' . $id . '/cancel');
	}
	
}

?>