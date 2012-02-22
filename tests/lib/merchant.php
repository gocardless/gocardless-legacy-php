<?php

class Test_Merchant extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
			'access_token'	=> 'TOKEN123',
			'merchant_id'	=> '123',
		);
		
		// Set the environment to TEST
		GoCardless::$environment = 'sandbox';
		
		$this->client = new GoCardless_Client($this->config);
	}

	public function testMerchantMethods() {

		foreach (array('subscriptions', 'pre_authorizations', 'users', 'payments', 'bills') as $method)
		{
			$merchant = new GoCardless_Merchant($this->client);

			$data = array(
				'id' => 1, 
				'id' => 2
			);
			
			/* TODO Ask Harry 
			stub_get(@client, data)

			merchant.send(method).should be_a Array
			merchant.send(method).length.should == 2
			merchant.send(method).zip(data).each do |obj,attrs|
			  class_name = GoCardless::Utils.camelize(method.to_s).sub(/s$/, '')
			  obj.class.to_s.should == "GoCardless::#{class_name}"
			  attrs.each { |k,v| obj.send(k).should == v }
			end
			*/
		}

		// $this->assertEquals('https://sandbox.gocardless.com', GoCardless::$base_url);
	}

}
