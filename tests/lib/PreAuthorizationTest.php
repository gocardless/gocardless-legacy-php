<?php

class Test_PreAuthorization extends PHPUnit_Framework_TestCase {

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
		$preauthorization_url = GoCardless::$client->base_url . '/api/v1/pre_authorizations/123';

		$stub->staticExpects($this->once())
			->method('get')
			->with($this->equalTo($preauthorization_url));

		GoCardless_PreAuthorization::find('123');

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

    $preauth = GoCardless_PreAuthorization::find_with_client(GoCardless::$client, '123');

    $this->assertInstanceOf('GoCardless_PreAuthorization', $preauth);
    $this->assertEquals('123', $preauth->id);

	}

	/**
	 * Tests creating bills under a preauth
	 */
	public function testCreateBill() {

    // Create a mock for the get method of GoCardless_Request
    $get_stub = $this->getMock('GoCardless_Request', array('get'));

    // Static dependency injection
    GoCardless::setClass('Request', get_class($get_stub));

    // Test finding a pre-authorization uses get
		$get_stub->staticExpects($this->once())
			->method('get')
			->will($this->returnValue(array('id' => '123')));

    // Load a mock pre-authorization
    $preauth = GoCardless_PreAuthorization::find('123');

    // Create a mock for the post method of GoCardless_Request
    $put_stub = $this->getMock('GoCardless_Request', array('post'));

    // Static dependency injection
    GoCardless::setClass('Request', get_class($put_stub));

    // Test that create bill uses post
		$put_stub->staticExpects($this->once())
			->method('post')
			->will($this->returnValue(array('id' => '123')));

    // Call the preauth->cancel() method
    $result = $preauth->create_bill(array(
      'amount' => '10'
    ));

    // Test that create bill returns a bill object
    $this->assertInstanceOf('GoCardless_Bill', $result);

	}

	/**
	 * Tests for preauth->cancel
	 */
	public function testCancel() {

    // Create a mock for the get method of GoCardless_Request
    $get_stub = $this->getMock('GoCardless_Request', array('get'));

    // Static dependency injection
    GoCardless::setClass('Request', get_class($get_stub));

    // Test finding a pre-authorization uses get
		$get_stub->staticExpects($this->once())
			->method('get')
			->will($this->returnValue(array('id' => '123')));

    // Load a mock pre-authorization
    $preauth = GoCardless_PreAuthorization::find('123');

    // Create a mock for the put method of GoCardless_Request
    $put_stub = $this->getMock('GoCardless_Request', array('put'));

    // Static dependency injection
    GoCardless::setClass('Request', get_class($put_stub));

    // Test that cancel a preauth uses put
		$put_stub->staticExpects($this->once())
			->method('put')
			->will($this->returnValue(array('id' => '123')));

    // Call the preauth->cancel() method
    $result = $preauth->cancel();

    // Test that cancel returns the preauth object
    $this->assertInstanceOf('GoCardless_PreAuthorization', $result);

    // Test that cancel returns the right id
    $this->assertEquals('123', $result->id);

	}

}
