<?php

/**
 * This is a demo of a typical merchant integration with GoCardless.
 *
 * We also have a partner system here. More info here:
 * http://blog.gocardless.com/post/19695292096
 * See partner.php for the partner demo.
 *
 * Setup
 *
 * 1. Sign up for an account at GoCardless.com
 * 2. Copy your app id and secret from the developer tab and paste them below
 * 3. Change 'Redirect URI' in the developer tab to point to this page
 *
 */

// Include library
include_once '../lib/GoCardless.php';

// Sandbox
GoCardless::$environment = 'sandbox';

// Config vars
$account_details = array(
  'app_id'        => null,
  'app_secret'    => null,
  'merchant_id'   => null,
  'access_token'  => null
);

// Fail nicely if no account details set
if ( ! $account_details['app_id'] && ! $account_details['app_secret']) {
  echo '<p>First sign up to <a href="http://gocardless.com">GoCardless</a> and
copy your sandbox API credentials from the \'Developer\' tab into the top of
this script.</p>';
  exit();
}

// Initialize GoCardless
GoCardless::set_account_details($account_details);

if (isset($_GET['resource_id']) && isset($_GET['resource_type'])) {
  // Get vars found so let's try confirming payment

  $confirm_params = array(
    'resource_id'   => $_GET['resource_id'],
    'resource_type' => $_GET['resource_type'],
    'resource_uri'  => $_GET['resource_uri'],
    'signature'     => $_GET['signature']
  );

  // State is optional
  if (isset($_GET['state'])) {
    $confirm_params['state'] = $_GET['state'];
  }

  $confirmed_resource = GoCardless::confirm_resource($confirm_params);

  echo '<p>Payment confirmed:<br /><pre>';
  print_r($confirmed_resource);
  echo '</pre></p>';

}

echo '<h2>New payment URLs</h2>';

// New bill

$payment_details = array(
  'amount'  => '30.00',
  'name'    => 'Donation'
);

$bill_url = GoCardless::new_bill_url($payment_details);
echo '<p><a href="' . $bill_url . '">New bill</a>';

// New subscription

$payment_details = array(
  'amount'          => '10.00',
  'interval_length' => 1,
  'interval_unit'   => 'month'
);

$subscription_url = GoCardless::new_subscription_url($payment_details);
echo ' &middot; <a href="' . $subscription_url . '">New subscription</a>';

// New pre-authorization

$payment_details = array(
  'max_amount'      => '100.00',
  'interval_length' => 1,
  'interval_unit'   => 'month',
  'user'    => array(
    'first_name'  => 'Tom',
    'last_name'   => 'Blomfield',
    'email'       => 'tom@gocardless.com'
    )
);

$pre_auth_url = GoCardless::new_pre_authorization_url($payment_details);
echo ' &middot; <a href="' . $pre_auth_url . '">New pre-authorized payment</a></p>';

echo '<p>NB. The \'new pre-authorization\' link is also a demo of pre-populated
user data.</p>';

// API calls

echo '<h2>API calls</h2>';

// Merchant API call

echo 'GoCardless_Merchant::find(\'' . $account_details['merchant_id'] . '\')';
echo '<blockquote><pre>';
$merchant = GoCardless_Merchant::find($account_details['merchant_id']);
print_r($merchant);
echo '</pre></blockquote>';

// Pre-authorization API calls

echo 'GoCardless_Merchant::find(\'' . $account_details['merchant_id'] . '\')->pre_authorizations()';
echo '<blockquote><pre>';
$preauths = GoCardless_Merchant::find($account_details['merchant_id'])->pre_authorizations();
print_r($preauths);
echo '</pre></blockquote>';

// Create a pre-auth using the link generated above then fetch it's ID
// using the query above. Now you can create bills within that pre-auth
// like this:

//echo 'GoCardless_PreAuthorization::find(\'123\')->create_bill($bill_details)';
//echo '<blockquote><pre>';
//$pre_auth = GoCardless_PreAuthorization::find('123');
//$bill_details = array(
//  'amount'  => '1.00'
//);
//$bill = $pre_auth->create_bill($bill_details);
//print_r($bill);
//echo '</pre></blockquote>';

// Filtering API results

// You can also fetch bills created within that pre-auth using the following
// code. NB. This is an example of filtering API results. More info here:
// https://gocardless.com/docs/api_guide#filtering

//echo 'GoCardless_Merchant::find(\'' . $account_details['merchant_id'] . '\')->bills(array(\'source_id\' => \'123\'))';
//echo '<blockquote><pre>';
//$bills = GoCardless_Merchant::find($account_details['merchant_id'])->bills(array('source_id' => '123'));
//print_r($bills);
//echo '</pre></blockquote>';

// Subscriptions API calls

echo 'GoCardless_Merchant::find(\'' . $account_details['merchant_id'] . '\')->subscriptions()';
echo '<blockquote><pre>';
$subscriptions = GoCardless_Merchant::find($account_details['merchant_id'])->subscriptions();
print_r($subscriptions);
echo '</pre></blockquote>';

// Create a subscription using the url generated above then fetch it's ID
// using the query above. Now you can cancel subscriptions using the
// following:

//echo 'GoCardless_Subscription::find('123')->cancel()';
//echo '<blockquote><pre>';
//$sub = GoCardless_Subscription::find('123')->cancel();
//print_r($sub);
//echo '</pre></blockquote>';
