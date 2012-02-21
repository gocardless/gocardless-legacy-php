<?php

class Test_Client extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
			'redirect_uri'	=> 'http://test.com/cb'
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
	
	public function testBaseUrlSetManually() {
		
		$this->config['base_url'] = 'https://abc.gocardless.com';
		
		$this->_GoCardless = new GoCardless_Client($this->config);
		
		$this->assertEquals('https://abc.gocardless.com', $this->_GoCardless->base_url);
	}

	}

}
