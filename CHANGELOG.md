## 0.4.2 - January 15, 2014

- Adds support for charge_customer_at and cancelling payments
- Clearer comments, docs and general bug fixes

## 0.4.1 - May 24, 2013

- Adds support for payouts API in GoCardless_Payout class, and though payout() method on GoCardless_Bill
- Adds example of using pagination in the API (see lines 154-164 for examples/merchant.php)

## 0.4.0 - April 24, 2013

- Defaults to sandbox environment if none is specified
- Adds support for pre-population when creating a merchant as a partner (see the [docs](https://gocardless.com/docs/partner_guide#prepopulating-information) for further details)
- Updates defaults to make requests as secure as possible in older versions of Curl (*CURLOPT_SSL_VERIFYPEER* is explicitly set to true)
- Makes API exceptions more accessible by adding the methods `getJson()`, `getResponse()` and `getError()` to the class (see [issue #18](https://github.com/gocardless/gocardless-php/pull/18) for further details)

## 0.3.5 - April 16, 2013

- Adds support for retrying a failed by via the API

## 0.3.4 - March 14, 2013

- Adds some debugging notes to Request.php to resolve an issue with PUT
requests in some versions of PHP

## 0.3.3 - August 2, 2012

- Bundled SSL certificates with library.

## 0.3.2 - May 28, 2012

- Added debugging lines for SSL certificate issues

## 0.3.1 - April 27, 2012

- Tweaked user agent syntax
- Improved curl debugger
- Improved API error message reporting
- Create subresource methods dynamically
- Tweaks to sample code in /examples


## 0.3.0 - April 11, 2012

- Confirm resource returns correct object type
- Webhook demo logs invalid webhooks and returns 403
- Application specific tag can be added to request user agent


## 0.2.3 - April 3, 2012

- Added ability to filter index results
- Tweaks to sample code in /examples


## 0.2.2 - March 31, 2012

- Fix for create_bill method in PreAuthorization class
- Fix for users method in Merchant class
- Fix for find_with_client in PreAuthorization class
- Added user agent to requests
- Improved test coverage


## 0.2.1 - March 28, 2012

- Fix for find method in Bill class
- Fix for bills method in Merchant class
- Removed PHPSpec
- Added more PHPUnit tests


## 0.2.0 - March 21, 2012

- Fixed cancelling subscriptions
- Abstracted out Request class
- Started using PHPSpec
- Improved PSR-0 compatibility
- Add `state` support to `new_{subscription,pre_authorization,bill}_url`
- Improved merchant and partner demos
- Added webhook demo


## 0.1.1 - February 27, 2012

- Fixed merchant subscription and user methods


## 0.1.0 - February 20, 2012

- Initial release
