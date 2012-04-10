<?php

/**
 * This is a demo of the webhook functionality of GoCardless.
 *
 * You can use this script with the webhook testing tool in the developer tab.
 * At the moment, the best way to learn about the different webhooks is to
 * change the options in the webhook tester and read the annotations that pop
 * up.
 *
 * Webhook documentation:
 * https://sandbox.gocardless.com/docs/web_hooks_guide
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

// Initialize GoCardless
GoCardless::set_account_details($account_details);

// Use this line to fetch the body of the HTTP request
$webhook = file_get_contents('php://input');

// Or use this json blog for testing
//$webhook = '{
//    "payload": {
//        "resource_type": "bill",
//        "action": "paid",
//        "bills": [
//            {
//                "id": "AKJ398H8KA",
//                "status": "paid",
//                "source_type": "subscription",
//                "source_id": "KKJ398H8K8",
//                "paid_at": "2011-12-01T12:00:00Z",
//                "uri": "https://sandbox.gocardless.com/api/v1/bills/AKJ398H8KA"
//            },
//            {
//                "id": "AKJ398H8KB",
//                "status": "paid",
//                "source_type": "subscription",
//                "source_id": "8AKJ398H78",
//                "paid_at": "2011-12-09T12:00:00Z",
//                "uri": "https://sandbox.gocardless.com/api/v1/bills/AKJ398H8KB"
//            }
//        ],
//        "signature": "f6b9e6cd8eef30c444da48370e646839c9bb9e1cf10ea16164d5cf93a50231eb"
//    }
//}';

// Convert json blog to array
$webhook_array = json_decode($webhook, true);

// Validate webhook
$webhook_valid = GoCardless::validate_webhook($webhook_array['payload']);

// Write webhook to a file for inspection
// You'll probably need to create this file and make it writable
$log = fopen('webhooks.txt', 'a');

if ($webhook_valid != TRUE) {
  fwrite($log, "Invalid webhook:\n\n");
}

fwrite($log, print_r($webhook_array, TRUE) . "\n\n");
fclose($log);

if ($webhook_valid == TRUE) {

  // Send a success header
  header('HTTP/1.1 200 OK');

} else {

  header('HTTP/1.1 403 Invalid signature');

}
