<?php

class Test_Utils extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
			'access_token'	=> 'foo',
			'merchant_id'	=> '123',
		);

		GoCardless::$environment = 'sandbox';

	}

	/**
	 * Check the signature is being generated correctly
	 */
	public function testGenerateSignature() {

		GoCardless::set_account_details($this->config);

		$payment_details = array(
		  'amount'          => '10.00',
		  'interval_length' => 1,
		  'interval_unit'   => 'month'
		);

		$sig = GoCardless_Utils::generate_signature($payment_details, $this->config['app_secret']);

		// Check against a pre-built hash
		$this->assertEquals('889b12a1aa31ca4c804d8554d4991fccc2d6d269bca4a20ecde6ef0cc20abc9c', $sig);

	}

}
