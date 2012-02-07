<?php

// First create your application in the GoCardless sandbox:
// https://sandbox.gocardless.com
// Then grab your application identifier and secret...

// Include library
include_once 'gocardless.php';

// ...and paste them in here

// Config vars
$gocardless_config = array(
	'app_id'		=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
	'app_secret'	=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+',
	'access_token'	=> '+vJh7dkHLr5rbdqBLlRk3dPALyn0uvAKTMvRnfWOAKcQ6WRCx/QGsdOefGqEs6h6',
	'environment'	=> 'sandbox',
	'redirect_uri'	=> 'http://localhost:8888/demo.php'
);

// Initialize GoCardless
$gocardless = new GoCardless($gocardless_config);

if ($_GET) {
	// Can haz get vars so time to confirm payment
	
	if (isset($_GET['resource_id']) && isset($_GET['resource_type'])) {
		
		$confirm_result = $gocardless->confirm_resource($_GET['resource_id'], $_GET['resource_type']);
		$confirm = json_decode($confirm_result, true);
		
		if ($confirm['result'] == TRUE) {
			
			echo '<p>Payment confirmed!</p>';
			
		} else {
			
			echo 'Confirm result:';
			echo '<pre>';
			var_dump($confirm_result);
			echo '</pre>';			
			
		}
		
		
	}
	
}

echo '<h2>New payment URLs</h2>';

// New subscription

$payment_details = array(
	'amount'			=> '10.00',
	'merchant_id'		=> '258584',
	'interval_length'	=> 1,
	'interval_unit'		=> 'month'
);

echo '<p><a href="'.$gocardless->new_subscription_url($payment_details).'">New subscription</a>';

// New pre-authorization

$payment_details = array(
	'max_amount'		=> '20.00',
	'merchant_id'		=> '258584',
	'interval_length'	=> 1,
	'interval_unit'		=> 'month'
);

echo ' &middot; <a href="'.$gocardless->new_pre_authorization_url($payment_details).'">New pre-authorized payment</a>';

// New bill

$payment_details = array(
	'amount'		=> '20.00',
	'merchant_id'	=> '258584'
);

echo ' &middot; <a href="'.$gocardless->new_bill_url($payment_details).'">New bill</a></p>';

echo '<h2>API calls</h2>';

echo '$gocardless->merchant->get(258584)';
echo '<blockquote><pre>';
print_r($gocardless->merchant->get(258584));
echo '</pre></blockquote>';

?>