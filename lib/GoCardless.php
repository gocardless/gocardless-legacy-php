<?php

/**
 * GoCardless PHP library, core class
 *
 * @package GoCardless
 */

if ( ! function_exists('curl_init')) {
  throw new Exception('GoCardless needs the CURL PHP extension.');
}
if ( ! function_exists('json_decode')) {
  throw new Exception('GoCardless needs the JSON PHP extension.');
}

// Autoload sub-classes
spl_autoload_register(array('GoCardless', 'autoload'));

require 'GoCardless/Exceptions.php';

/**
 * GoCardless class
 *
 */
class GoCardless {

  /**
   *
   * @var constant VERSION
   */
  const VERSION = '0.4.1';

  /**
   *
   * @var string $environment
   */
  public static $environment;

  /**
   * The environment: 'sandbox' (default) or 'production'
   *
   * @var object $client
   */
  public static $client;

  /**
   * Class References
   * Help map references to static classes for use in mocking
   *
   * @var array $classes
   */
  protected static $classes = array(
    'Request' => 'GoCardless_Request',
  );

  /**
   * Set the class to use
   *
   * @param string $name The nickname of the class to load
   * @param object $class The class to load
   */
  public static function setClass($name, $class) {
    self::$classes[$name] = $class;
  }

  /**
   * Get the class to use
   *
   * @param string $name The nickname of the class to get
   *
   * @return The loaded class
   */
  public static function getClass($name) {
    return self::$classes[$name];
  }

  /**
   * Autoload sub-classes
   *
   * @param string $class Name of the class to load
   */
  public static function autoload($class) {
    if (strpos($class, 'GoCardless') === 0) {
      require str_replace('_', '/', $class).'.php';
    }
  }

  /**
   * Initialization function called with account details
   *
   * @param array $account_details Array of account details
   */
  public static function set_account_details($account_details) {
    GoCardless::$client = new GoCardless_Client($account_details);
  }

  /**
   * Generate a URL to give a user to create a new bill
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public static function new_bill_url($params) {
    return GoCardless::$client->new_bill_url($params);
  }

  /**
   * Generate a URL to give a user to create a new subscription
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public static function new_subscription_url($params) {
    return GoCardless::$client->new_subscription_url($params);
  }

  /**
   * Generate a URL to give a user to create a new pre-authorized payment
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public static function new_pre_authorization_url($params) {
    return GoCardless::$client->new_pre_authorization_url($params);
  }

  /**
   * Generate a URL to give a user to create a new bill
   *
   * @param array $params Parameters to use to generate the URL
   *
   * @return string The generated URL
   */
  public static function confirm_resource($params) {
    return GoCardless::$client->confirm_resource($params);
  }

  /**
   * Validate the payload of a webhook
   *
   * @param array $params The payload of the webhook
   *
   * @return boolean True if webhook signature is valid
   */
  public static function validate_webhook($params) {
    return GoCardless::$client->validate_webhook($params);
  }

}
