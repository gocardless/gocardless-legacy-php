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
require 'lib/bill.php';
require 'lib/client.php';
require 'lib/merchant.php';
require 'lib/subscription.php';
require 'lib/pre_authorization.php';
require 'lib/oauth.php';

/**
 * GoCardless class
 *
 */
abstract class GoCardless {
	
	/** @type string The environment: sandbox or live */
	public static $environment;
	
	/** @type array Array of account details */
	public static $account_details;
	
	/** @type object The client object */
	public static $client;
	
	/**
	 * Initialization function called with account details
	 *
	 * $account_details array Array of account details
	 */
	public static function setAccountDetails($account_details) {
		
		foreach ($account_details as $key => $value) {
			self::$account_details[$key] = $value;
		}
		
		GoCardless::$client = new GoCardless_Client(self::$account_details);
		
	}
	
	/**
	 * Generate a URL to give a user to create a new subscription
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function newSubscriptionUrl($params) {
		return GoCardless::$client->newSubscriptionUrl($params);
	}
	
	/**
	 * Generate a URL to give a user to create a new pre-authorized payment
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function newPreAuthorizationUrl($params) {
		return GoCardless::$client->newPreAuthorizationUrl($params);
	}
	
	/**
	 * Generate a URL to give a user to create a new bill
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function newBillUrl($params) {
		return GoCardless::$client->newBillUrl($params);
	}
	
	/**
	 * Generate a URL to give a user to create a new bill
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public static function confirmResource($params) {
		return GoCardless::$client->confirmResource($params);
	}
	
	/**
	 * Validate the payload of a webhook
	 *
	 * @param array $params The payload of the webhook
	 *
	 * @return boolean True if webhook signature is valid
	 */
	public static function validateWebhook($params) {
		return GoCardless::$client->validateWebhook($params);
	}
	
}

?>