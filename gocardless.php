<?php

/**
 * GoCardless PHP library
 *
 * @package  GoCardless
 */

if (!function_exists('curl_init')) {
  throw new Exception('GoCardless needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('GoCardless needs the JSON PHP extension.');
}

// Include subclasses
require 'lib/utils.php';
require 'lib/exceptions.php';
require 'lib/client.php';
require 'lib/resource.php';
require 'lib/merchant.php';
require 'lib/subscription.php';
require 'lib/pre_authorization.php';
require 'lib/bill.php';

/**
 * Global client var
 */
global $client;

/**
 * GoCardless class
 *
 */
abstract class GoCardless {
	
	/**
	 * Environment var: live or sandbox
	 */
	public static $environment;
	
	/**
	 * Array of account details
	 */
	public static $account_details = array();
	
	/**
	 * Initialization function called with account details
	 *
	 * $param array Array of account details
	 */
	public static function set_account_details($account_details) {
		global $client;
		foreach ($account_details as $key => $value) {
			self::$account_details[$key] = $value;
		}
		$client = new Client(self::$account_details);
	}
	
	// PUBLIC FUNCTIONS
	
	/**
	 * Generate a URL to give a user to create a new subscription
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function new_subscription_url($params) {
		return Client::new_subscription_url($params);
	}
	
	/**
	 * Generate a URL to give a user to create a new pre-authorized payment
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function new_pre_authorization_url($params) {
		return Client::new_pre_authorization_url($params);
	}
	
	/**
	 * Generate a URL to give a user to create a new bill
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function new_bill_url($params) {
		return Client::new_bill_url($params);
	}
	
	/**
	 * Generate a URL to give a user to create a new bill
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public function confirm_resource($params) {
		return Client::confirm_resource($params);
	}
	
	/**
	 * Validate the payload of a webhook
	 *
	 * @param array $params The payload of the webhook
	 *
	 * @return boolean True if webhook signature is valid
	 */
	public function validate_webhook($params) {
		return Client::validate_webhook($params);
	}
	
}

?>