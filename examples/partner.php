<?php

/**
 * This is a demo of a partner integration with GoCardless.
 *
 * For a typical merchant integration demo see merchant.php.
 *
 * More info about our partner system:
 * http://blog.gocardless.com/post/19695292096
 *
 * Setup
 *
 * 1. Sign up for an account at GoCardless.com
 * 2. Copy your app id and secret from the developer tab and paste them below
 * 3. Change 'Redirect URI' in the developer tab to point to this page and
 *    update it in both places in the code below
 * 4. Load the page and click 'authorize app' to generate an access_token
 * 5. You can now initialize a Client object using that access_token
 *
 * NB. You'll probably want to save the access_token in your database too
 *
 * This page does the following:
 *
 *  1. Generates an authorize link
 *  2. Generates an access_token (using $_GET['code'])
 *  3. Instantiates a Client object
 *  4. Fetches the current merchant's details
 *  5. Fetches the current merchant's pre-authorizations
 *  6. Creates a bill under a pre-authorizations
 *  7. Repeats steps 4 and 5 with a different Client object
 *
 */

// Include library
include_once '../lib/GoCardless.php';

// Sandbox
GoCardless::$environment = 'sandbox';

// Config vars for your PARTNER account
$account_details = array(
  'app_id'        => null,
  'app_secret'    => null,
//  'merchant_id'   => null,
//  'access_token'  => null
);

// Fail nicely if no account details set
if ( ! $account_details['app_id'] && ! $account_details['app_secret']) {
  echo '<p>First sign up to <a href="http://gocardless.com">GoCardless</a> and
copy your sandbox API credentials from the \'Developer\' tab into the top of
this script.</p>';
  exit();
}

// Initalize new Client object
$gocardless_client = new GoCardless_Client($account_details);

if (isset($_GET['code'])) {
  // Code being passed as a get var

  $params = array(
    'client_id'     => $account_details['app_id'],
    'code'          => $_GET['code'],
    'redirect_uri'  => 'http://localhost/examples/partner.php',
    'grant_type'    => 'authorization_code'
  );

  // Fetching token returns merchant_id and access_token
  $token = $gocardless_client->fetch_access_token($params);

  $account_details = array(
    'app_id'        => $account_details['app_id'],
    'app_secret'    => $account_details['app_secret'],
    'access_token'  => $token['access_token'],
    'merchant_id'   => $token['merchant_id']
  );

  // Create new Client object
  $gocardless_client = new GoCardless_Client($account_details);

  // Yay!
  echo '<p>Authorization successful!
  <br />Copy and paste this access token into the top of the code for this
  page to continue testing the partner demo. In your own app, you\'ll want to
  save it to your database.
  <br />Access token: ' . $account_details['access_token'] . '
  <br />Merchant id: ' . $account_details['merchant_id'] . '</p>';

}

if (isset($account_details['access_token'])) {
  // We have an access token, run some API queries using our shiny new token

  echo '<h2>Partner authorization</h2>';

  echo '<p>Access token found!</p>';

  echo '$gocardless_client->merchant()';
  echo '<blockquote><pre>';
  $merchant = $gocardless_client->merchant();
  print_r($merchant);
  echo '</pre></blockquote>';

  echo 'echo $gocardless_client->merchant()->pre_authorizations()';
  echo '<blockquote><pre>';
  $preauths = $gocardless_client->merchant()->pre_authorizations();
  print_r($preauths);
  echo '</pre></blockquote>';

  //echo '$gocardless_client->create_bill($pre_auth_details)';
  //echo '<blockquote><pre>';
  //$pre_auth_details = array(
  //  'pre_authorization_id'  => '123',
  //  'amount'                => '1.00'
  //);
  //$bill = $gocardless_client->create_bill($pre_auth_details);
  //print_r($bill);
  //echo '</pre></blockquote>';

  // Instantiate a second client object, just as an experiment
  $account_details = array(
    'app_id'        => $account_details['app_id'],
    'app_secret'    => $account_details['app_secret'],
    'merchant_id'   => null,
    'access_token'  => null
  );

  // Fail nicely if no second set of account details
  if (!isset($account_details['merchant_id'])
    || !isset($account_details['access_token'])) {
    echo '<p>To fully test partner mode, authorize a second merchant account
    and paste the merchant_id access_token into the $account_details for
    $gocardless_client2.</p>';
    exit();
  }

  $gocardless_client2 = new GoCardless_Client($account_details);

  echo 'echo $gocardless_client2->merchant()';
  echo '<blockquote><pre>';
  $merchant = $gocardless_client2->merchant();
  print_r($merchant);
  echo '</pre></blockquote>';

  echo '$gocardless_client2->merchant()->pre_authorizations()';
  echo '<blockquote><pre>';
  $preauths = $gocardless_client2->merchant()->pre_authorizations();
  print_r($preauths);
  echo '</pre></blockquote>';

} else {
  // No access token so show new authorization link

  echo '<h2>Partner authorization</h2>';
  $authorize_url_options = array(
    'redirect_uri' => 'http://localhost/examples/partner.php'
  );
  $authorize_url = $gocardless_client->authorize_url($authorize_url_options);
  echo '<p><a href="' . $authorize_url . '">Authorize app</a></p>';

}
