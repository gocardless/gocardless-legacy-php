<?php

class Test_GoCardless extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
			'access_token'	=> 'foo',
		);

	}

	/**
	 * Test that set_account_details creates an instance of client
	 */
	public function testSetAccountDetailsCreatesClient() {

    // Use sandbox
    GoCardless::$environment = 'sandbox';

		// Initialize GoCardless
		GoCardless::set_account_details($this->config);

		$this->assertInstanceOf('GoCardless_Client', GoCardless::$client);

	}

	/**
	 * Test that set_account_details fails without an app id
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testSetAccountDetailsFailsWithoutAppId() {

		$config = $this->config;

		// Remove the access token from config
		unset($config['app_id']);

    // Call set_account_details() knowing it will throw an exception
		GoCardless::set_account_details($config);

	}

	/**
	 * Test that set_account_details fails without an app secret
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testSetAccountDetailsFailsWithoutAppSecret() {

		$config = $this->config;

		// Remove the access token from config
		unset($config['app_secret']);

    // Call set_account_details() knowing it will throw an exception
		GoCardless::set_account_details($config);

	}

}
