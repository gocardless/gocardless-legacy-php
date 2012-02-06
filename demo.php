<?php

// Include library
include_once 'gocardless.php';

// Config vars
$gocardless_config = array(	'app_identifier'	=> 'eCxrcWDxjYsQ55zhsDTgs6VeKf6YWZP7be/9rY0PGFbeyqmLJV6k84SUQdISLUhf',
							'app_secret'		=> '2utXOc65Hy9dolp3urYBMoIN0DM11Q9uuoboFDkHY3nzsugqcuzD1FuJYA7X9TP+',
							'access_token'		=> '+vJh7dkHLr5rbdqBLlRk3dPALyn0uvAKTMvRnfWOAKcQ6WRCx/QGsdOefGqEs6h6'
							);

// Initialize objects
$gocardless = new GoCardless($gocardless_config);

// New subscription

$payment_details = array(	'amount'			=> '10.00',
							'merchant_id'		=> '258584',
							'interval_length'	=> 1,
							'interval_unit'		=> 'month'
							);

echo '<p><a href="'.$gocardless->generate_url('subscription', $payment_details).'">Subscribe to me</a></p>';

// New pre-authorization

$payment_details = array(	'max_amount'		=> '20.00',
							'merchant_id'		=> '258584',
							'interval_length'	=> 1,
							'interval_unit'		=> 'month'
							);

echo '<p><a href="'.$gocardless->generate_url('pre_authorization', $payment_details).'">Pre-auth me</a></p>';

// New bill

$payment_details = array(	'amount'		=> '20.00',
							'merchant_id'	=> '258584'
							);

echo '<p><a href="'.$gocardless->generate_url('bill', $payment_details).'">Pay me</a></p>';

?>