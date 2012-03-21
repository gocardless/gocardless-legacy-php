<?php

class DescribeGoCardless extends \PHPSpec\Context {

  private $_account_details;

  public function before() {

    $this->_account_details = array(
		  'app_id'        => 'X',
		  'app_secret'    => 'X',
		  'merchant_id'   => 1,
		  'access_token'  => 'X'
		);

  }

  public function itShouldThrowAnExceptionWhenAppIdIsMissing() {

    unset($this->_account_details['app_id']);

    //$this->spec(
    //  GoCardless::set_account_details($this->_account_details)
    //)->should->throwException();

  }

}