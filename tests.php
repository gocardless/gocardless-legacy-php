<?php

require_once 'gocardless.php';

class GoCardlessTest extends PHPUnit_Framework_TestCase {
	
	public $config;
	
	public function setUp() {
		
		$this->config = array(
			'merchant_id'	=> '258584',
			'app_id'		=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
			'app_secret'	=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+',
			'access_token'	=> '+vJh7dkHLr5rbdqBLlRk3dPALyn0uvAKTMvRnfWOAKcQ6WRCx/QGsdOefGqEs6h6',
			'environment'	=> 'sandbox',
			'redirect_uri'	=> 'http://localhost:8888/demo.php'
		);
		
	}
	
	// Porting tests from client.rb
	
	public function testBaseUrlInSandbox() {
		
		$this->config['environment'] = 'sandbox';
		
		$this->_GoCardless = new GoCardless($this->config);
		
		$this->assertEquals('https://sandbox.gocardless.com', $this->_GoCardless->base_url);
		
	}
	
	public function testBaseUrlInProduction() {
		
		$this->config['environment'] = 'production';
		
		$this->_GoCardless = new GoCardless($this->config);
		
		$this->assertEquals('https://gocardless.com', $this->_GoCardless->base_url);
		
	}
	
	public function testBaseUrlSetManually() {
		
		$this->config['base_url'] = 'https://abc.gocardless.com/';
		
		$this->_GoCardless = new GoCardless($this->config);
		
		$this->assertEquals('https://abc.gocardless.com/', $this->_GoCardless->base_url);
		
	}
	
	/**
     * @expectedException GoCardlessClientException
     */
	public function testExceptionWithNoAppId() {
		
		$this->config['app_id'] = null;
		
		$this->_GoCardless = new GoCardless($this->config);
		
	}
	
	/**
     * @expectedException GoCardlessClientException
     */
	public function testExceptionWithNoAppSecret() {
		
		$this->config['app_secret'] = null;
		
		$this->_GoCardless = new GoCardless($this->config);
		
	}
	
	// That's the first five from client.rb
	// Skipping
	//		#authorize_url x2
	//		#fetch_access_token x3
	//		#access_token x2
	//		#access_token x3
	
	// api_get
	
	public function testSendGetRequest() {
		
		$url = 'https://sandbox.gocardless.com/api/v1/merchants/258584';
		
		$mock = $this->getMock('GoCardless', array('make_request'));
		
		$mock->expects($this->once())
			->method('make_request')
			->with($this->equalTo($url));
		
		$this->_GoCardless = new GoCardless($this->config);
		$this->_GoCardless->attach($mock);
		$this->_GoCardless->merchant->find('258584');
		
		// Create a Mock Object for the Observer class
		// mocking only the update() method.
		//$observer = $this->getMock('Observer', array('update'));
		
		// Set up the expectation for the update() method
		// to be called only once and with the string 'something'
		// as its parameter.
		//$observer->expects($this->once())
		//         ->method('update')
		//         ->with($this->equalTo('something'));
		
		// Create a Subject object and attach the mocked
		// Observer object to it.
		//$subject = new Subject;
		//$subject->attach($observer);
		
		// Call the doSomething() method on the $subject object
		// which we expect to call the mocked Observer object's
		// update() method with the string 'something'.
		//$subject->doSomething();
		
	}
	
	public function testApiGetUsesCorrectPathPrefix() {
		
		$this->assertEquals('/api/v1', $this->_GoCardless->api_path);
		
	}
	
	///**
    // * @expectedException GoCardlessClientException
    // */
	//public function testApiGetFailsWithoutAccessToken() {
	//	
	//	$this->config['access_token'] = null;
	//	
	//	$this->_GoCardless = new GoCardless($this->config);
	//	
	//}
	
	
}

?>