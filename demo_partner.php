<?php

// First create your application in the GoCardless sandbox:
// https://sandbox.gocardless.com
// Then grab your application identifier and secret...

// Include library
include_once 'gocardless.php';

// ...and paste them in here:

// Sandbox
GoCardless::$environment = 'sandbox';

// Config vars for your PARTNER account
GoCardless::setAccountDetails(array(
	'app_id'			=> 'EuHqvzOJfD9NFSACSK8Q0ZfpwpmbyQao4NdYbgi0IidwlQQ_HzIgdrVZsjRUosNc',
	'app_secret'		=> 'KNa1GoyIKFwcNN_OVdN8D5ykZQkfnCVIyHCFBdP_iXquB7_O7WaZRTWRLhPGsCBQ'
));

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
	
	GoCardless::setAccountDetails(array(
		'app_id'			=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
		'app_secret'		=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+',
		'access_token'		=> $access_token,
		'merchant_id'		=> $merchant_id
	));
	
	//GoCardless::$account_details['merchant_id'] = $merchant_id;
	//GoCardless::$account_details['access_token'] = $access_token;
	
}

echo '<h2>Partner calls</h2>';
$url = OAuth::authorizeUrl();
echo '<p><a href="'.$url.'">Authorize app</a></p>';



?>