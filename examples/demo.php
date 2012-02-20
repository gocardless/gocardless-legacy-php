<?php

// First create your application in the GoCardless sandbox:
// https://sandbox.gocardless.com
// Then grab your application id and secret and paste them in below
// You'll also need to to change the various ids throughout this demo

// Include library
include_once 'gocardless.php';

// Sandbox
GoCardless::$environment = 'sandbox';

// Config vars
$account_details = array(
  'app_id'        => null,
  'app_secret'    => null,
  'merchant_id'   => null,
  'access_token'  => null
);
// Initialize GoCardless
GoCardless::set_account_details($account_details);

if (isset($_GET['resource_id']) && isset($_GET['resource_type'])) {
  // Get vars found so let's try confirming payment

  $confirm_params = array(
    'resource_id'   => $_GET['resource_id'],
    'resource_type' => $_GET['resource_type'],
    'signature'     => $_GET['signature']
  );

  // State is optional
  if (isset($_GET['state'])) {
    $confirm_params['state'] = $_GET['state'];
  }

  // resource_uri is optional
  if (isset($_GET['resource_uri'])) {
    $confirm_params['resource_uri'] = $_GET['resource_uri'];
  }

  $confirmed_resource = GoCardless::confirm_resource($confirm_params);

  echo '<p>Payment confirmed!</p>';

}

echo '<h2>New payment URLs</h2>';

// New subscription

$payment_details = array(
  'amount'          => '10.00',
  'interval_length' => 1,
  'interval_unit'   => 'month'
);

$subscription_url = GoCardless::new_subscription_url($payment_details);
echo '<p><a href="'.$subscription_url.'">New subscription</a>';

// New pre-authorization

$payment_details = array(
  'max_amount'      => '20.00',
  'interval_length' => 1,
  'interval_unit'   => 'month'
);

$pre_auth_url = GoCardless::new_pre_authorization_url($payment_details);
echo ' &middot; <a href="'.$pre_auth_url.'">New pre-authorized payment</a>';

// New bill

$payment_details = array(
  'amount'  => '30.00',
  'name'    => 'Donation',
  'user'    => array(
    'first_name'  => 'Tom',
    'last_name'   => 'Blomfield',
    'email'       => 'tom@gocardless.com'
    )
);

$bill_url = GoCardless::new_bill_url($payment_details);
echo ' &middot; <a href="'.$bill_url.'">New bill</a></p>';

echo 'NB. The \'new bill\' link is also a demo of pre-populated user data';

echo '<h2>API calls</h2>';

echo 'GoCardless_Merchant::find(\'258584\')';
echo '<blockquote><pre>';
$merchant = GoCardless_Merchant::find('258584');
print_r($merchant);
echo '</pre></blockquote>';

echo 'GoCardless_Merchant::find(\'258584\')->pre_authorizations()';
echo '<blockquote><pre>';
$preauths = GoCardless_Merchant::find('258584')->pre_authorizations();
print_r($preauths);
echo '</pre></blockquote>';

echo 'GoCardless_PreAuthorization::find(\'992869\')->create_bill($bill_details)';
echo '<blockquote><pre>';
$pre_auth = GoCardless_PreAuthorization::find('013M018V0K');
$bill_details = array(
  'amount'  => '15.00'
);
$bill = $pre_auth->create_bill($bill_details);
print_r($bill);
echo '</pre></blockquote>';

echo 'validate webhook:';
echo '<blockquote><pre>';
$webhook_json = '{"payload":{"bills":[{"id":"880807"},{"status":"pending"},{"source_type":"subscription"},{"source_id":"21"},{"uri":"https:\/\/sandbox.gocardless.com\/api\/v1\/bills\/880807"}],"action":"created","resource_type":"bill","signature":"f25a611fb9afbc272ab369ead52109edd8a88cbb29a3a00903ffbce0ec6be5cb"}}';
$webhook = json_decode($webhook_json, true);
var_dump(GoCardless::validate_webhook($webhook['payload']));
echo '</pre></blockquote>';

?>