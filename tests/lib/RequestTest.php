<?php

class Test_Request extends PHPUnit_Framework_TestCase {

	public function setUp() {

    // Set config without access_token
		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
		);

		GoCardless::$environment = 'sandbox';
	  GoCardless::set_account_details($this->config);

	}

	/**
	 * Get requests without an access_token should fail
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testApiGetFailsWithoutAccessToken() {

		// Create a mock for the get method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('get'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

		// Call Merchant class, knowing it will throw an exception
		GoCardless_Merchant::find('123');

	}

	/**
	 * Post requests without an access_token should fail
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testApiPostFailsWithoutAccessToken() {

		// Create a mock for the post method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('post'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

    // Call create_bill() knowing it will throw an exception
		$bill = GoCardless::$client->create_bill(array(
		  'pre_authorization_id'  => '123',
			'amount'                => '5.00'
		));

  }

	/**
	 * Post requests without an access_token should fail
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testApiPutFailsWithoutAccessToken() {

		// Create a mock for the post method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('put'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

    // Call cancel subscription knowing it will throw an exception
		$subscription = GoCardless_Subscription::find('123')->cancel();

  }

}
