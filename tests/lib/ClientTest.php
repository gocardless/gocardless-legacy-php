<?php

class Test_Client extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->config = array(
			'app_id'		=> 'abc',
			'app_secret'	=> 'xyz',
			'access_token'	=> 'foo',
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

		$this->assertEquals($params['response_type'], 'code');
	  $this->assertEquals($params['redirect_uri'], $redirect_uri);
	  $this->assertEquals($params['client_id'], $this->config['app_id']);

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

}
