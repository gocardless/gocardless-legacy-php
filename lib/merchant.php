<?php

class Merchant {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function get($id) {
		return $this->gocardless->fetch_resource('merchants/' . $id);
	}
	
	public function subscriptions($id) {
		return $this->gocardless->fetch_resource('merchants/' . $id . '/subscriptions');
	}
	
	public function pre_authorizations($id) {
		return $this->gocardless->fetch_resource('merchants/ ' . $id . '/pre_authorizations');
	}
	
	public function users($id) {
		return $this->gocardless->fetch_resource('merchants/' . $id . '/users');
	}
	
	public function bills($id) {
		return $this->gocardless->fetch_resource('merchants/' . $id . '/bills');
	}
	
	public function payments($id) {
		return $this->gocardless->fetch_resource('merchants/' . $id . '/payments');
	}
	
}

?>