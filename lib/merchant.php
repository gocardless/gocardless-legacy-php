<?php

class Merchant {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function get($merchant_id) {
		return $this->gocardless->fetch_resource('merchants/' . $merchant_id);
	}
	
	public function subscriptions($merchant_id) {
		return $this->gocardless->fetch_resource('merchants/' . $merchant_id . '/subscriptions');
	}
	
	public function pre_authorizations($merchant_id) {
		return $this->gocardless->fetch_resource('merchants/ ' . $merchant_id . '/pre_authorizations');
	}
	
	public function users($merchant_id) {
		return $this->gocardless->fetch_resource('merchants/' . $merchant_id . '/users');
	}
	
	public function bills($merchant_id) {
		return $this->gocardless->fetch_resource('merchants/' . $merchant_id . '/bills');
	}
	
	public function payments($merchant_id) {
		return $this->gocardless->fetch_resource('merchants/' . $merchant_id . '/payments');
	}
	
}

?>