<?php

class DescribeClient extends \PHPSpec\Context {

  private $_account_details;

  public function before() {

    $this->_account_details = array(
		  'app_id'        => 'X',
		  'app_secret'    => 'X',
		  'merchant_id'   => 1,
		  'access_token'  => 'X'
		);

  }

  public function itShouldReturnCorrectProductionUrl() {

    GoCardless::$environment = 'production';

    $client = $this->spec(new GoCardless_Client($this->_account_details));

    $client->base_url->should->equal('https://gocardless.com');

  }

  public function itShouldReturnCorrectSandboxUrl() {

    GoCardless::$environment = 'sandbox';

    $client = $this->spec(new GoCardless_Client($this->_account_details));

    $client->base_url->should->equal('https://sandbox.gocardless.com');

  }

}
