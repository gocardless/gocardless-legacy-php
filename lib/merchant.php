<?php

class Merchant {
	
	public function __construct($gocardless) {
		
		echo '<pre>';
		print_r($gocardless);
		
		if (!isset($gocardless)) {
			// Fail, no config vars
		}
		
		foreach ($gocardless as $key => $value) {
			$this->{$key} = $value;
		}
		
		// If environment is not set then default to production
		if (!isset($gocardless->environment)) {
			$this->environment = 'production';
		}
		
		// Set base_url based on environment
		$this->base_url = $gocardless->base_urls[$gocardless->environment];
		
	}
	
	public function subscriptions($params) {
		
		return $params['id'];
		//return $this->app_identifier;
		//return parent::generate_url('subscription', $payment_details);
		
	}
	
}

?>