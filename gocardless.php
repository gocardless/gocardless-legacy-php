<?php

// Include subclasses
include_once 'lib/utils.php';
include_once 'lib/resource.php';
include_once 'lib/client.php';
include_once 'lib/merchant.php';
include_once 'lib/subscription.php';
include_once 'lib/pre_authorization.php';
include_once 'lib/bill.php';

class GoCardless {
	
	public static $environment;
	public static $account_details;
	
	//public static $redirect_uri;
	//public static $response_format;
	
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
	public function new_pre_authorization_url($params) {
		return Client::new_pre_authorization_url($params);
	}
	
	/**
	 * Generate a URL to give a user to create a new bill
	 *
	 * @param array $params Parameters to use to generate the URL
	 *
	 * @return string The generated URL
	 */
	public function new_bill_url($params) {
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

	public function validate_webhook($params) {
		return Client::validate_webhook($params);
	}
	
}

// EXCEPTIONS

class GoCardlessClientException extends Exception {
	
	// Default error settings
	public function __construct($description = 'Unknown client error') {
		
		// Throw an exception
		parent::__construct($description);
		
	}
		
}

class GoCardlessArgumentsException extends Exception {
	
	// Default error settings
	public function __construct($description = 'Unknown argument error') {
		
		// Throw an exception
		parent::__construct($description);
		
	}
		
}

class GoCardlessApiException extends Exception {
	
	// Default error settings
	public function __construct($description = 'Unknown error', $code = 0) {
		
		// Description is sometimes an empty array
		if (empty($description)) {
			$description = 'Unknown error';
		}
		
		// Throw an exception
		parent::__construct($description, $code);
		
	}
		
}

?>