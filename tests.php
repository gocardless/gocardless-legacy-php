<?php

require_once 'gocardless.php';

class GoCardlessTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
		
		$gocardless_config = array(
			'app_id'		=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
			'app_secret'	=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+',
			'access_token'	=> '+vJh7dkHLr5rbdqBLlRk3dPALyn0uvAKTMvRnfWOAKcQ6WRCx/QGsdOefGqEs6h6',
			'environment'	=> 'sandbox',
			'redirect_uri'	=> 'http://localhost:8888/demo.php'
		);
		
		$this->_GoCardless = new GoCardless($gocardless_config);
		
	}
	
	public function testBaseUrlInSandbox() {
		$_GoCardless->base_url = $_GoCardless->base_urls['sandbox'];
		$this->assertEquals('https://sandbox.gocardless.com', $_GoCardless->base_url);
	}
	
    function testThatItWorks() {
        self::assertTrue(false);
    }
	
    function testThatItDoesntWork() {
        self::assertTrue(true);
    }
	
}

?>