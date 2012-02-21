<?php

class Test_Client extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
			'app_secret'	=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+',
			'merchant_id'	=> '258584',
			'access_token'	=> '+vJh7dkHLr5rbdqBLlRk3dPALyn0uvAKTMvRnfWOAKcQ6WRCx/QGsdOefGqEs6h6'
		);
	}

	// Porting tests from client.rb

	public function testBaseUrlInSandbox() {

		// Set the environment to TEST
		GoCardless::$environment = 'sandbox';

		$this->_GoCardless = new GoCardless_Client($this->config);

		$this->assertEquals('https://sandbox.gocardless.com', $this->_GoCardless->base_url);

		/* If this is a static, do this 
		$this->assertAttributeEquals(
			'https://sandbox.gocardless.com',
			'base_url', 
			$this->_GoCardless
		);
		*/
	}

	public function testBaseUrlInProduction() {

		// Set the environment to TEST
		GoCardless::$environment = 'production';

		$this->_GoCardless = new GoCardless_Client($this->config);

		$this->assertEquals('https://gocardless.com', $this->_GoCardless->base_url);

	}

}
