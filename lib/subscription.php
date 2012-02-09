<?php

class Subscription {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function get($id) {
		$endpoint = 'subscriptions/' . $id;
		return $this->gocardless->fetch_resource($endpoint);
	}
	
	public function cancel($id) {
		$endpoint = 'subscriptions/' . $id . '/cancel';
		return $this->gocardless->fetch_resource($endpoint);
	}
	
}

?>