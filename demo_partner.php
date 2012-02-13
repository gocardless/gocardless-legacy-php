<?php

// First create your application in the GoCardless sandbox:
// https://sandbox.gocardless.com
// Then grab your application identifier and secret...

// Include library
include_once 'gocardless.php';

// ...and paste them in here:

// Sandbox
GoCardless::$environment = 'sandbox';

// Config vars, stripe style
GoCardless::setAccountDetails(array(
	'app_id'			=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
	'app_secret'		=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+'
));

echo '<h2>Partner calls</h2>';

echo 'Get new authorisation url:<br />client.new_merchant_url(array(\'redirect_uri\' => \'http://mywebsite.com/cb\'))';
echo '<blockquote><pre>';
$client = new_merchant_url(array('redirect_uri' => 'http://mywebsite.com/cb'));
//$merchant = GoCardless_Merchant::find('258584');
//print_r($merchant);
echo '</pre></blockquote>';

?>