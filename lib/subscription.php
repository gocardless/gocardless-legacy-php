<?php

class Subscription {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function get($id) {
		return $this->gocardless->fetch_resource('subscriptions/' . $id);
	}
	
}

?>