<?php

class Test_Bill extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
			'access_token'	=> 'foo',
		);

		// Assign as a method for the next test
		GoCardless::set_account_details($this->config);

	}

	/**
	 * Find bill with source returns bill objects
	 */
	public function testGetWithSource() {

    //$bill = GoCardless_Bill::find('123');
    //
    //$this->assertEquals(get_class_name($bill), 'GoCardless_Bill')''

	}

	/**
	 * Find bill with source returns bill objects
	 */
	public function testSetWithSource() {

    //$bill = GoCardless_Bill::find('123');
    //$subscription = new GoCardless_Subscription('123');
    //
    //$this->assertEquals($bill['source_id'], $subscription['id']);

	}

}
