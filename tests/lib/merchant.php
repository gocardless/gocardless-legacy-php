<?php

class Test_Merchant extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
			'access_token'	=> 'foo',
		);
	
		// Set the environment to TEST
		GoCardless::$environment = 'sandbox';
	}
	
	/**
	 * Fails without an access_token
	 */
	public function testLookupMerchant()
	{
		// Remove the access token from config
		$config = $this->config;
		
		// Assign as a method for the next test
		GoCardless::set_account_details($config);
		
		// Create a Mock Object for the Observer class
		// mocking only the update() method.
		$stub = $this->getMock('GoCardless_Request', array('get'));
		
		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));
		
		// Expected URL
		$merchant_url = GoCardless::$client->base_url . '/api/v1/merchants/123';
		
		$stub->staticExpects($this->once())
			->method('get')
			->with($this->equalTo($merchant_url));
		
		// Call Merchant class, knowning it will throw an exception
		GoCardless_Merchant::find('123');
	}

}
