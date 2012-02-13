<?php

/**
 * GoCardless PHP library, core class
 *
 * @package GoCardless
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

/** @type object The client object */
global $client;

/**
 * GoCardless class
 *
 */
abstract class GoCardless {
	
	/** @type string The environment: sandbox or live */
	public static $environment;
	
	/** @type array Array of account details */
	public static $account_details;
	
	/**
	 * Initialization function called with account details
	 *
	 * $account_details array Array of account details
	 */
	public static function set_account_details($account_details) {
		global $client;
		foreach ($account_details as $key => $value) {
			self::$account_details[$key] = $value;
		}
		$client = new Client(self::$account_details);
	}
	
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
	public static function confirm_resource($params) {
		return Client::confirm_resource($params);
	}
	
	/**
	 * Validate the payload of a webhook
	 *
	 * @param array $params The payload of the webhook
	 *
	 * @return boolean True if webhook signature is valid
	 */
	public static function validate_webhook($params) {
		return Client::validate_webhook($params);
	}
	
}

?>