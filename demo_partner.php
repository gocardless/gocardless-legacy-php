<?php

// First create your partner application in the GoCardless sandbox:
// https://sandbox.gocardless.com

// Then grab your application identifier and secret and paste them in below

// Now test the 'authorize app' link which will generate an access_token

// Save access_token and merchant_id in your database against the current user
// And use them to initialize GoCardless for that user

// NB. You can also paste in access_token and merchant_id below for testing


// Include library
include_once 'gocardless.php';

// Sandbox
GoCardless::$environment = 'sandbox';

// Config vars for your PARTNER account
$account_details = array(
	'app_id'			=> 'EuHqvzOJfD9NFSACSK8Q0ZfpwpmbyQao4NdYbgi0IidwlQQ_HzIgdrVZsjRUosNc',
	'app_secret'		=> 'KNa1GoyIKFwcNN_OVdN8D5ykZQkfnCVIyHCFBdP_iXquB7_O7WaZRTWRLhPGsCBQ',
	'merchant_id'		=> null,
	'access_token'		=> null
);

GoCardless::setAccountDetails($account_details);

if ($_GET['code']) {
	
	$params = array(
		'client_id'		=> GoCardless::$account_details['app_id'],
		'code'			=> $_GET['code'],
		'redirect_uri'	=> 'http://localhost:8888/demo_partner.php',
		'grant_type'	=> 'authorization_code'
	);
	
	$response = OAuth::fetchAccessToken($params);
	
	$token_response = json_decode($response, true);
	
	$merchant = explode(':', $token_response['scope']);
	$merchant_id = $merchant[1];
	
	$access_token = $token_response['access_token'];
	
	$account_details = array(
		'app_id'			=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
		'app_secret'		=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+',
		'access_token'		=> $access_token,
		'merchant_id'		=> $merchant_id
	);
	
	GoCardless::setAccountDetails($account_details);
	
	echo "<p>Authorization successful!
	<br />Add the following to your database for this merchant
	<br />Access token: $access_token
	<br />Merchant id: $merchant_id</p>";
	
}

echo '<h2>Partner authorization</h2>';
$url = OAuth::authorizeUrl();
echo '<p><a href="'.$url.'">Authorize app</a></p>';

if ($account_details['access_token']) {
	
	echo '<h2>Partner API calls</h2>';
	
	$payment_details = array(
		'amount'			=> '10.00',
		'interval_length'	=> 1,
		'interval_unit'		=> 'month'
	);
	
	$subscription_url = $client->newSubscriptionUrl($payment_details);
	echo '<p><a href="'.$subscription_url.'">New subscription</a></p>';
	
	echo '$client->bill(\'992375\')';
	echo '<blockquote><pre>';
	$bill = $client->bill('992375');
	print_r($bill);
	echo '</pre></blockquote>';
	
}

?>