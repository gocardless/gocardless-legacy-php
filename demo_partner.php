<?php

/**
 * First create your partner application in the GoCardless sandbox:
 * https://sandbox.gocardless.com
 *
 * Then grab your application identifier and secret and paste them in below
 *
 * Now test the 'authorize app' link which will generate an access_token
 *
 * Save access_token and merchant_id in your database against the current user
 * And use them to initialize GoCardless for that user
 *
 * NB. You can also paste in access_token and merchant_id below for testing
 * 
 * 
 * This page then does the following:
 * 
 *  1. Shows an authorize link
 *  2. Generates an access_token from the retured $_GET['code']
 *  3. Instantiate new GoCardless_Client object
 *  (4. Check for GET vars required to confirm a payment)
 *  5. Show new bill url
 *  6. Fetch a bill from the API
 *  7. 
 * 
*/



// Include library
include_once 'gocardless.php';

// Sandbox
GoCardless::$environment = 'sandbox';

// Config vars for your PARTNER account
$account_details = array(
	'app_id'			=> 'EuHqvzOJfD9NFSACSK8Q0ZfpwpmbyQao4NdYbgi0IidwlQQ_HzIgdrVZsjRUosNc',
	'app_secret'		=> 'KNa1GoyIKFwcNN_OVdN8D5ykZQkfnCVIyHCFBdP_iXquB7_O7WaZRTWRLhPGsCBQ',
	'merchant_id'		=> '258584',
	'access_token'		=> 'ShFGfoXO+GOKEiMeoeMPUvSTTq9HC0v3BRLXo8eSUifbZOtneYqqNECPa+QK6AdL'
);

GoCardless::setAccountDetails($account_details);

if (isset($_GET['code'])) {
	
	$params = array(
		'client_id'		=> GoCardless::$account_details['app_id'],
		'code'			=> $_GET['code'],
		'redirect_uri'	=> 'http://localhost:8888/demo_partner.php',
		'grant_type'	=> 'authorization_code'
	);
	
	// Fetching token returns merchant_id and access_token
	$token = GoCardless::$client->fetchAccessToken($params);
	
	$account_details = array(
		'app_id'			=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
		'app_secret'		=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+',
		'access_token'		=> $token['access_token'],
		'merchant_id'		=> $token['merchant_id']
	);
	
	GoCardless::setAccountDetails($account_details);
	
	echo '<p>Authorization successful!
	<br />Add the following to your database for this merchant
	<br />Access token: '.$token['access_token'].'
	<br />Merchant id: '.$token['merchant_id'].'</p>';
	
}

if ($account_details['access_token']) {
	// We have an access token

	echo '<h2>Partner authorization</h2>';

	echo '<p>Access token found, make connection:</p>';
	
	echo "<blockquote><pre>\$account_details should now contain merchant_id and access_token:\n\n";
	print_r($account_details);
	echo "\n\n";
	echo '$gocardless_client = new GoCardless_Client($account_details);';
	echo '</pre></blockquote>';
	
	// Instantiate GoCardless_Client object with new access_token
	$gocardless_client = new GoCardless_Client($account_details);
	
	/*
	// Get vars found so let's try confirming payment
	if (isset($_GET['resource_id']) && isset($_GET['resource_type'])) {

		$confirm_params = array(
			'resource_id'	=> $_GET['resource_id'],
			'resource_type'	=> $_GET['resource_type'],
			'signature'		=> $_GET['signature']
		);

		// State is optional
		if (isset($_GET['state'])) {
			$confirm_params['state'] = $_GET['state'];
		}

		// resource_uri is optional
		if (isset($_GET['resource_uri'])) {
			$confirm_params['resource_uri'] = $_GET['resource_uri'];
		}

		$confirm = $gocardless_client->confirmResource($confirm_params);

		$confirm_decoded = json_decode($confirm, true);

		if ($confirm_decoded['success'] == TRUE) {

			echo '<p>Payment confirmed!</p>';

		} else {

			echo 'Payment not confirmed, following message was returned:';
			echo '<pre>';
			var_dump($confirm);
			echo '</pre>';

		}

	}
	*/
	
	echo '<h2>Partner API calls</h2>';
	
	$payment_details = array(
		'amount' => '10.00'
	);
	
	$bill_url = $gocardless_client->newBillUrl($payment_details);
	echo '<p><a href="'.$bill_url.'">New bill</a></p>';
	
	echo '$gocardless_client->bill(\'992375\')';
	echo '<blockquote><pre>';
	$bill = $gocardless_client->bill('992375');
	print_r($bill);
	echo '</pre></blockquote>';
	
	// pre_auth = client.pre_authorization(2250)
	// pre_auth.create_bill(:name => "150 credits", :amount => "15.00")
	
	$bill_details = array(
		'bill' => array(
			'amount'				=> '10.00',
			'pre_authorization_id'	=> 'UQSTF7AMQMYWBL'
		)
	);
	
	echo '$gocardless_client->create_bill($bill_details)';
	echo '<blockquote><pre>';
	$bill = $gocardless_client->createBill($bill_details);
	print_r($bill);
	echo '</pre></blockquote>';
	
} else {
	// No access token so show new authorization link
	
	echo '<h2>Partner authorization</h2>';
	$authorize_url_options = array(
		'redirect_uri' => 'http://localhost:8888/demo_partner.php'
	);
	$url = GoCardless::$client->authorizeUrl($authorize_url_options);
	echo '<p><a href="'.$url.'">Authorize app</a></p>';
	
}

?>