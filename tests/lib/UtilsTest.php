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
		GoCardless::set_account_details($this->config);

	}

	/**
	 * Check camelize function
	 */
	public function testCamelize() {

	  $return = GoCardless_Utils::camelize('a_test_string');

    $this->assertEquals('ATestString', $return);

  }

	/**
	 * Check singularize function
	 */
	public function testSingularize() {

	  $a = GoCardless_Utils::singularize('desks');
    $this->assertEquals('desk', $a);

    $b = GoCardless_Utils::singularize('cacti');
    $this->assertEquals('cactus', $b);

  }

	/**
	 * Test generate_query_string joins items with equals
	 */
	public function testGenerateQueryStringJoinsItemsWithEquals() {

    $params = array('a' => 'b');

  	$return = GoCardless_Utils::generate_query_string($params);

    $this->assertEquals("a=b", $return);

  }

	/**
	 * Test generate_query_string joins pairs with ampersands
	 */
	public function testGenerateQueryStringJoinsPairsWithAmpersands() {

    $params = array('a' => 'b', 'c' => 'd');

  	$return = GoCardless_Utils::generate_query_string($params);

    $this->assertEquals("a=b&c=d", $return);

  }


  /**
   *
   * Test for correct sorting of keys/values
   */
  public function testGenerateQueryStringSortsByKeyThenValue() {

    $params = array(
      'bills' => array(
        array(
          'amount' => 10.0,
          'amount_minus_fees' => 9.9,
        ),
        array(
          'amount' => 19.99,
          'amount_minus_fees' => 19.79,
        ),
      ),
    );


    $sig = GoCardless_Utils::generate_query_string($params);

    // Check against a pre-built hash
    $this->assertEquals('bills%5B%5D%5Bamount%5D=10&bills%5B%5D%5Bamount%5D=19.99&bills%5B%5D%5Bamount_minus_fees%5D=19.79&bills%5B%5D%5Bamount_minus_fees%5D=9.9', $sig);
  }

	/**
	 * Check the signature is being generated correctly
	 */
	public function testGenerateSignature() {

		$payment_details = array(
		  'amount'          => '10.00',
		  'interval_length' => 1,
		  'interval_unit'   => 'month'
		);

		$sig = GoCardless_Utils::generate_signature($payment_details, $this->config['app_secret']);

		// Check against a pre-built hash
		$this->assertEquals('889b12a1aa31ca4c804d8554d4991fccc2d6d269bca4a20ecde6ef0cc20abc9c', $sig);

	}

	/**
	 * Check generate_query_string returns empty string when empty array passed
	 */
	public function testGenerateQueryStringReturnWhenEmptyArrayPassed() {

    $return = GoCardless_Utils::generate_query_string(array());

    $this->assertEquals('', $return);

  }

	/**
	 * Check generate_query_string works with integer keys and values
	 */
	public function testGenerateQueryStringWorksWithIntegerKeysAndValues() {

    //$params = array('123' => '456');
    //
	  //$return = GoCardless_Utils::generate_query_string($params);
    //
    //$this->assertEquals('123=456', $return);

  }

}
