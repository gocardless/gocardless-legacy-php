<?php

class Test_Merchant extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
			'access_token'	=> 'foo',
		);

		GoCardless::$environment = 'sandbox';

	}

	/**
	 * Fails without an access_token
	 */
	public function testLookupMerchant() {

		$config = $this->config;

		GoCardless::set_account_details($config);

		// Create a mock for the get method of GoCardless_Request
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
