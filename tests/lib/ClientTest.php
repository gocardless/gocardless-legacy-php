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

		// Set custo base_url
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

    // Call authorize_url() knowing it will throw an exception
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

/*

  describe "#fetch_access_token" do

      it "sets @access_token" do
        access_token = mock
        access_token.stubs(:params).returns('scope' => '')
        access_token.stubs(:token).returns('')

        oauth_client = @client.instance_variable_get(:@oauth_client)
        oauth_client.auth_code.expects(:get_token).returns(access_token)

        @client.instance_variable_get(:@access_token).should be_nil
        @client.fetch_access_token('code', {:redirect_uri => @redirect_uri})
        @client.instance_variable_get(:@access_token).should == access_token
      end
    end
  end

  describe "#access_token" do
    it "serializes access token correctly" do
      oauth_client = @client.instance_variable_get(:@oauth_client)
      token = OAuth2::AccessToken.new(oauth_client, 'TOKEN123')
      token.params['scope'] = 'a:1 b:2'
      @client.instance_variable_set(:@access_token, token)

      @client.access_token.should == 'TOKEN123 a:1 b:2'
    end

    it "returns nil when there's no token" do
      @client.access_token.should be_nil
    end
  end

  describe "#access_token=" do
    it "deserializes access token correctly" do
      @client.access_token = 'TOKEN123 a:1 b:2'
      token = @client.instance_variable_get(:@access_token)
      token.token.should == 'TOKEN123'
      token.params['scope'].should == 'a:1 b:2'
    end

    it "ignores 'bearer' if it is present at the start of the string" do
      @client.access_token = 'Bearer TOKEN manage_merchant:123'
      token = @client.instance_variable_get(:@access_token)
      token.token.should == 'TOKEN'
      token.params['scope'].should == 'manage_merchant:123'
    end

    it "handles invalid values correctly" do
      token = 'TOKEN123'  # missing scope
      expect { @client.access_token = token }.to raise_exception ArgumentError
    end
  end


	*/

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
	 * Get requests without an access_token should fail
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testApiGetFailsWithoutAccessToken() {

		$config = $this->config;

		// Remove the access token from config
		unset($config['access_token']);

		GoCardless::set_account_details($config);

		// Create a mock for the get method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('get'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

		// Call Merchant class, knowing it will throw an exception
		GoCardless_Merchant::find('123');

	}

	/**
	 * Post requests without an access_token should fail
	 *
	 * @expectedException GoCardless_ClientException
	 */
	public function testApiPostFailsWithoutAccessToken() {

		$config = $this->config;

		// Remove access_token from config vars
		unset($config['access_token']);

		GoCardless::set_account_details($config);

		// Create a mock for the post method of GoCardless_Request
		$stub = $this->getMock('GoCardless_Request', array('post'));

		// Static dependency injection
		GoCardless::setClass('Request', get_class($stub));

    // Call create_bill() knowing it will throw an exception
		$bill = GoCardless::$client->create_bill(array(
		  'pre_authorization_id'  => '123',
			'amount'                => '5.00'
		));

  }

}
