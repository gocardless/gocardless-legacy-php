<?php

class Test_Client extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'        => 'abc',
			'app_secret'    => 'xyz',
			'access_token'  => 'foo',
			'merchant_id'   => 'bar',
		);

		GoCardless::$environment = 'sandbox';

	}

	/**
	 * base_url correct in sandbox
	 */
	public function testBaseUrlInSandbox() {

		$client = new GoCardless_Client($this->config);

		$this->assertEquals('https://sandbox.gocardless.com', $client->base_url);

	}

	/**
	 * base_url correct in production
	 */
	public function testBaseUrlInProduction() {

		// Set environment to production
		GoCardless::$environment = 'production';

		$client = new GoCardless_Client($this->config);

		$this->assertEquals('https://gocardless.com', $client->base_url);

		// Set environment back to sandbox for remaining tests
		GoCardless::$environment = 'sandbox';

	}

	/**
	 * Ensure custom base_url can be set
	 */
	public function testBaseUrlSetManually() {

		$config = $this->config;

		// Set custom base_url
		$config['base_url'] = 'https://abc.gocardless.com';

		$client = new GoCardless_Client($config);

		$this->assertEquals('https://abc.gocardless.com', $client->base_url);

	}

	/**
	 * Test if an exception is thrown when app_id is not passed to Client
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testNoAppIdError() {

		$config = $this->config;

		// Remove app_id from config
		unset($config['app_id']);

    // Instantiate new Client knowing it will throw an exception
		new GoCardless_Client($config);

	}

	/**
	 * Test if an exception is thrown when app_secret is not passed to Client
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testNoAppSecretError() {

		$config = $this->config;

		// Remove app_secret from config
		unset($config['app_secret']);

    // Instantiate new Client knowing it will throw an exception
		new GoCardless_Client($config);

	}

	/**
	 * Test if an exception is thrown when redirect_uri is not passed to
	 * authorize_url
	 *
	 * @expectedException GoCardless_ArgumentsException
	 */
	public function testNoRedirectUriError() {

		$client = new GoCardless_Client($this->config);

    // Call authorize_url() without passing redirect_uri as an argument
    // knowing it will throw an exception
		$client->authorize_url();

	}

	/**
	 * Generate authorize_url correct
	 */
	public function testGenerateAuthUrl() {

		$client = new GoCardless_Client($this->config);

		$redirect_uri = 'http://test.com/cb';

		$url = $client->authorize_url(array(
			'redirect_uri' => $redirect_uri,
		));

		$parts = parse_url($url);
		parse_str($parts['query'], $params);

		$this->assertEquals('code', $params['response_type']);
	  $this->assertEquals($redirect_uri, $params['redirect_uri']);
	  $this->assertEquals($this->config['app_id'], $params['client_id']);

	}

	/**
	 * Test that fetch_access_token works with correct arguments
	 */
	public function testFetchAccessTokenArguments() {

		$client = new GoCardless_Client(array(
		  'app_id'        => '123',
		  'app_secret'    => 'abc',
		));

		// Create a mock for the post method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('post'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

		// Set up the expectation for the post() method to be called
		$stub->staticExpects($this->once())
			->method('post')
			->with($this->matchesRegularExpression('#/oauth/access_token#'));

		// Fetching token returns merchant_id and access_token
		$token = $client->fetch_access_token(array(
			'client_id'     => $this->config['app_id'],
			'code'          => 'fakecode',
			'redirect_uri'  => 'http://localhost/examples/partner.php',
			'grant_type'    => 'authorization_code'
		));

		$this->arrayHasKey($token, 'access_token');
		$this->arrayHasKey($token, 'merchant_id');

	}

  /**
  * Ensure API url is set up correctly
  */
	public function testApiUrlFormation() {

		GoCardless::set_account_details($this->config);

		// Create a mock for the get method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('get'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

		// Set up the expectation for the get() method to be called
		$stub->staticExpects($this->once())
			->method('get')
			->with($this->matchesRegularExpression('#api/v1/#'));

		// Call Merchant class, knowing it will use our mock to request
		GoCardless_Merchant::find('123');

	}

	/**
  * Test nonces are random
  */
	public function testNoncesAreRandom() {

		GoCardless::set_account_details($this->config);

    $this->assertNotEquals(
      GoCardless::$client->generate_nonce(),
      GoCardless::$client->generate_nonce()
    );

	}

	/**
  * Test new_limit_url uses correct path
  */
	public function testNewLimitUrlUsesCorrectPath() {

    GoCardless::set_account_details($this->config);

    $url = GoCardless::$client->new_limit_url('test_limit', array(
      'amount'  => '30.00',
    ));

    $parts = parse_url($url);
    $this->assertEquals('/connect/test_limits/new', $parts['path']);

	}

	/**
  * Test new_limit_url includes params in url
  */
	public function testNewLimitUrlIncludesParamsInUrl() {

    GoCardless::set_account_details($this->config);

    $params = array('a' => '1', 'b' => '2');

    $url = GoCardless::$client->new_limit_url('subscription', $params);

    $parts = parse_url($url);
		parse_str($parts['query'], $url_params);

		foreach ($params as $key => $value) {
		  $this->assertEquals($value, $url_params['subscription'][$key]);
		}

	}

	/**
  * Test new_limit_url includes state in url
  */
	public function testNewLimitUrlIncludesState() {

    GoCardless::set_account_details($this->config);

    $params = array('a' => '1', 'b' => '2', 'state' => 'monkey');

    $url = GoCardless::$client->new_limit_url('subscription', $params);

    $parts = parse_url($url);
		parse_str($parts['query'], $url_params);

		$this->assertEquals('monkey', $url_params['state']);

	}

	/**
  * Test new_limit_url includes redirect_uri in url
  */
	public function testNewLimitUrlIncludesRedirectUri() {

    GoCardless::set_account_details($this->config);

    $params = array(
      'a' => '1',
      'b' => '2',
      'redirect_uri' => 'http://www.google.com'
    );

    $url = GoCardless::$client->new_limit_url('subscription', $params);

    $parts = parse_url($url);
		parse_str($parts['query'], $url_params);

		$this->assertEquals('http://www.google.com', $url_params['redirect_uri']);

	}

	/**
  * Test new_limit_url adds in merchant_id
  */
	public function testNewLimitUrlIncludesMerchantId() {

    GoCardless::set_account_details($this->config);

    $params = array('a' => '1', 'b' => '2');

    $url = GoCardless::$client->new_limit_url('subscription', $params);

    $parts = parse_url($url);
		parse_str($parts['query'], $url_params);

	  $this->assertEquals('bar', $url_params['subscription']['merchant_id']);

	}

	/**
  * Test new_limit_url uses a valid sig
  */
	public function testNewLimitUrlIncludesValidSig() {

    GoCardless::set_account_details($this->config);

    $params = array('a' => '1', 'b' => '2');

    $url = GoCardless::$client->new_limit_url('subscription', $params);

    $parts = parse_url($url);
		parse_str($parts['query'], $url_params);
    $returned_sig = $url_params['signature'];
    unset($url_params['signature']);

	  $sig = GoCardless_Utils::generate_signature($url_params,
	      $this->config['app_secret']);

	  $this->assertNotEmpty($returned_sig);
    $this->assertEquals($sig, $returned_sig);

	}

	/**
  * Test new_limit_url uses a nonce
  */
	public function testNewLimitUrlIncludesNonce() {

    GoCardless::set_account_details($this->config);

    $url = GoCardless::$client->new_limit_url('subscription',
      array('x' => '1'));

    $parts = parse_url($url);
		parse_str($parts['query'], $url_params);

	  $this->assertNotEmpty($url_params['nonce']);

	}

	/**
  * Test new_limit_url adds in client_id
  */
	public function testNewLimitUrlIncludesClientId() {

    GoCardless::set_account_details($this->config);

    $url = GoCardless::$client->new_limit_url('subscription',
      array('x' => '1'));

    $parts = parse_url($url);
		parse_str($parts['query'], $url_params);

	  $this->assertNotEmpty($url_params['client_id']);

	}

	/**
  * Test new_limit_url adds valid timestamp
  */
	public function testNewLimitUrlIncludesTimestamp() {

    GoCardless::set_account_details($this->config);

    $date = new DateTime(null, new DateTimeZone('UTC'));
    $timestamp = $date->format('Y-m-d\TH:i:s\Z');

    $url = GoCardless::$client->new_limit_url('subscription',
      array('x' => '1'));

    $parts = parse_url($url);
		parse_str($parts['query'], $url_params);

    $this->assertStringMatchesFormat($timestamp, $url_params['timestamp']);

	}

	/**
  * Test validate_webhook returns false if sig is invalid
  */
	public function testValidateWebhookReturnsFalseIfInvalid() {

    GoCardless::set_account_details($this->config);

    $result = GoCardless::$client->validate_webhook(array(
      'some'      => 'stuff',
      'signature' => 'fail',
    ));

    $this->assertFalse($result);

	}

	/**
  * Test validate_webhook returns true if sig is valid
  */
	public function testValidateWebhookReturnsTrueIfValid() {

    GoCardless::set_account_details($this->config);

    $result = GoCardless::$client->validate_webhook(array(
      'some'      => 'stuff',
      'signature' => '175e814f0f64e5e86d41fb8fe06a857cedda715a96d3dc3d885e6d97dbeb7e49',
    ));

    $this->assertTrue($result);

	}

}
