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

		GoCardless_Subscription::find('123');

	}

	/**
	 * Test that find_with_client returns the correct object
	 */
	public function testFindWithClient() {

		// Create a mock for the get method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('get'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

		$stub->staticExpects($this->any())
			->method('get')
			->will($this->returnValue(array('id' => '123')));

    $subscription = GoCardless_Subscription::find_with_client(GoCardless::$client, '123');

    $this->assertInstanceOf('GoCardless_Subscription', $subscription);
    $this->assertEquals('123', $subscription->id);

	}

	/**
	 * Tests for subscription->cancel
	 */
	public function testCancel() {

    // Create a mock for the get method of GoCardless_Request
    $get_stub = $this->getMock('GoCardless_Request', array('get'));

    // Static dependency injection
    GoCardless::setClass('Request', get_class($get_stub));

    // Test finding a subscription uses get
		$get_stub->staticExpects($this->once())
			->method('get')
			->will($this->returnValue(array('id' => '123')));

    // Load a mock subscription
    $subscription = GoCardless_Subscription::find('123');

    // Create a mock for the put method of GoCardless_Request
    $put_stub = $this->getMock('GoCardless_Request', array('put'));

    // Static dependency injection
    GoCardless::setClass('Request', get_class($put_stub));

    // Test that cancel a subscription uses put
		$put_stub->staticExpects($this->once())
			->method('put')
			->will($this->returnValue(array('id' => '123')));

    // Call the subscription->cancel() method
    $result = $subscription->cancel();

    // Test that cancel returns the subscription object
    $this->assertInstanceOf('GoCardless_Subscription', $result);

    // Test that cancel returns the right id
    $this->assertEquals('123', $result->id);

	}

}
