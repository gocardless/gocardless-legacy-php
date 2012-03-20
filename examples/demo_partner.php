<?php

/**
 * Sign up for an account at GoCardless.com
 * Email help@gocardless.com to upgrade to a partner account
 * Copy your app id and secret from the developer tab and paste them in below
 *
 * Then grab your application identifier and secret and paste them in below
 *
 * Now test the 'authorize app' link which will generate an access_token
 *
 * Save access_token and merchant_id in your database against the current user
 * And use them to initialize GoCardless for that user
 *
 * NB. You can then paste in access_token and merchant_id below for testing
 * And you may want to replace the ids in the various API calls too.
 *
 *
 * This page then does the following:
 *
 *  1. Generates an authorize link
 *  2. Generates an access_token from the retured $_GET['code']
 *  3. Instantiates a new GoCardless_Client object
 *  4. Fetch the current merchant's details
 *  5. Fetch the current merchant's pre-authorizations
 *  6. Create a bill under a pre-authorizations
 *  7. Repeat steps 4 and 5 with a new GoCardless_Client object
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
  'access_token'  => null,
  'merchant_id'   => null
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
    'redirect_uri'  => 'http://localhost/examples/demo_partner.php',
    'grant_type'    => 'authorization_code'
  );

  // Fetching token returns merchant_id and access_token
  $token = $gocardless_client->fetch_access_token($params);

  $account_details = array(
    'app_id'        => null,
    'app_secret'    => null,
    'access_token'  => null,
    'merchant_id'   => null
  );

  // Create new Client object
  $gocardless_client = new GoCardless_Client($account_details);

  // Yay!
  echo '<p>Authorization successful!
  <br />Add the following to your database for this merchant
  <br />Access token: '.$token['access_token'].'
  <br />Merchant id: '.$token['merchant_id'].'</p>';

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

  echo '$gocardless_client->create_bill($pre_auth_details)';
  echo '<blockquote><pre>';
  $pre_auth_details = array(
    'pre_authorization_id'  => '014PS77JW3',
    'amount'                => '5.00'
  );
  $bill = $gocardless_client->create_bill($pre_auth_details);
  print_r($bill);
  echo '</pre></blockquote>';

  $account_details = array(
    'app_id'        => null,
    'app_secret'    => null,
    'access_token'  => null,
    'merchant_id'   => null
  );

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
    'redirect_uri' => 'http://localhost/examples/demo_partner.php'
  );
  $authorize_url = $gocardless_client->authorize_url($authorize_url_options);
  echo '<p><a href="'.$authorize_url.'">Authorize app</a></p>';

}
