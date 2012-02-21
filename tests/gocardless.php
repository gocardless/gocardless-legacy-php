<?php

class Test_GoCardless extends PHPUnit_Framework_TestCase {

	public function setUp() {



	}


	public function testAccountDetails()
	{
		// Initialize GoCardless
		GoCardless::set_account_details(array(
		  'app_id'        => 'X',
		  'app_secret'    => 'X',
		  'merchant_id'   => 1,
		  'access_token'  => 'X',
		));

		$class = get_class(GoCardless::$client);

		$this->assertEquals('GoCardless_Client', $class);
	}

/*
    it "gets upset if the token is missing" do
      expect {
        subject.account_details = @details.merge(:token => nil)
      }.to raise_exception GoCardless::ClientError
    end
  end


  describe "delegated methods" do
    %w(new_subscription_url new_pre_authorization_url new_bill_url confirm_resource webhook_valid?).each do |name|
      it "#{name} delegates to @client" do
        subject.account_details = @details
        subject.instance_variable_get(:@client).expects(name.to_sym)
        subject.send(name)
      end

      it "raises an exception if the account details aren't set" do
        expect {
          subject.send(name)
        }.to raise_exception GoCardless::ClientError
      end
    end
  end
*/

}
