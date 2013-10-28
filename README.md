[![GoCardless PHP Client Library](https://s3-eu-west-1.amazonaws.com/gocardless/images/client-lib-headers/php-lib-header.png)](https://gocardless.com/docs?language=php)

The GoCardless PHP client provides a simple PHP interface to the GoCardless
API.

The following links may be useful:

- [Documentation](https://gocardless.com/docs/php/merchant_client_guide) and [tutorial](https://gocardless.com/docs/php/merchant_tutorial_bill) for individual merchants
- [Documentation](https://gocardless.com/docs/php/partner_client_guide) for [partners](https://help.gocardless.com/what-is-the-partner-programme/) (who manage multiple merchant accounts)
- Our developer [help centre](https://help.gocardless.com/developer/)
- Our [introductory guide](https://gocardless.com/blog/gocardless-php-library/) to using the PHP library
- Some more [advanced PHP library usage](https://gocardless.com/blog/a-second-look-at-the-gocardless-php-library/)
- [Code samples](https://github.com/gocardless/gocardless-php/tree/master/examples)
- Our CodeIgniter [plugin](https://github.com/gocardless/codeigniter-gocardless) and [spark](http://getsparks.org/packages/GoCardless/versions/HEAD/show)
- You can also use GoCardless via the [PHP Payments](https://github.com/calvinfroedge/PHP-Payments) library and [CodeIgniter Payments](http://getsparks.org/packages/codeigniter-payments/versions/HEAD/show) spark
- [Full library reference](http://gocardless.github.com/gocardless-php/)

### Upgrade notes

Since 0.4.0, the default environment for the library is sandbox, not production.

To make your code work for production use, you'll then need to explicitly set the environment to production. Simply add this line of code before the call to `GoCardless::set_account_details`:

```php
GoCardless::$environment = 'production';
```

### Installation

The files you need to use the GoCardless API are in the /lib folder.

#### Install from source

```console
$ git clone git://github.com/gocardless/gocardless-php.git
```

#### Installing from the tarball

```console
$ curl -L https://github.com/downloads/gocardless/gocardless-php/gocardless-php-v0.4.1.tgz | tar xzv
```

#### Download the Zip

[Click here](https://github.com/gocardless/gocardless-php/zipball/v0.4.1)
to download the zip file.

#### Installing with Composer

Add `gocardless/gocardless` to the contents of your composer.json:

```javascript
{
    "require": {
        "gocardless/gocardless": ">=0.4.1"
    }
}
```

[![Build Status](https://secure.travis-ci.org/gocardless/gocardless-php.png?branch=master)](http://travis-ci.org/gocardless/gocardless-php)
