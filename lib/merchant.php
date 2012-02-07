<?php

class Merchant {
	
	public function __construct(&$gocardless) {
		$this->gocardless = $gocardless;
	}
	
	public function subscriptions($params) {
		
		return $params['id'];
		//return $this->gocardless->app_identifier;
		//return parent::generate_url('subscription', $payment_details);
		
	}
	
}

?>