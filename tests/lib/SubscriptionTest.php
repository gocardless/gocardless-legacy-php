<?php

class Test_Subscription extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
      'app_id'		=> 'abc',
      'app_secret'	=> 'xyz',
      'access_token'  => 'foo'
		);

    GoCardless::$environment = 'sandbox';
	  GoCardless::set_account_details($this->config);

	}

	/**
	 * Test that find generates the correct url
	 */
	public function testFind() {

		// Create a mock for the get method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('get'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

		// Expected URL
		$subscription_url = GoCardless::$client->base_url . '/api/v1/subscriptions/123';

		$stub->staticExpects($this->once())
			->method('get')
			->with($this->equalTo($subscription_url));

    // Call Subscription class, knowing it will throw an exception
		GoCardless_Subscription::find('123');

	}

	/**
	 * Test that find_with_client returns the correct object
	 */
	public function testFindWithClientInstantiatesCorrectObject() {

		// Create a mock for the GET method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('get'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

    $subscription = GoCardless_Subscription::find_with_client(GoCardless::$client, '123');

    $this->assertInstanceOf('GoCardless_Subscription', $subscription);

	}

}
