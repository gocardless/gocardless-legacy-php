[![GoCardless PHP Client Library](https://s3-eu-west-1.amazonaws.com/gocardless/images/client-lib-headers/php-lib-header.png)](https://developer.gocardless.com/php/#getting-started)

The GoCardless PHP client provides a simple PHP interface to the GoCardless
API.

The following links may be useful:

- [Developer overview](http://blog.gocardless.com/post/19695292096/goingcardless-an-introduction-to-gocardless-for) of GoCardless
- [Documentation and tutorials](https://developer.gocardless.com/php/#getting-started) for individual merchants
- [Documentation](https://developer.gocardless.com/php/#partner-guide) for [partners](https://help.gocardless.com/what-is-the-partner-programme/) (apps managing multiple merchants)
- Our quick [guide](https://help.gocardless.com/how-can-i-handle-exceptions-in-the-php-library/) to handling exceptions with the library
- [Code samples](https://github.com/gocardless/gocardless-php/tree/master/examples)
- Our CodeIgniter [plugin](https://github.com/gocardless/codeigniter-gocardless) and [spark](http://getsparks.org/packages/GoCardless/versions/HEAD/show) *(not supported)*
- You can also use GoCardless via the [PHP Payments](https://github.com/calvinfroedge/PHP-Payments) library and [CodeIgniter Payments](http://getsparks.org/packages/codeigniter-payments/versions/HEAD/show) spark
- [Full library reference](http://gocardless.github.com/gocardless-php/)

You can also use GoCardless via the [PHP Payments](https://github.com/calvinfroedge/PHP-Payments) library and [CodeIgniter Payments](http://getsparks.org/packages/codeigniter-payments/versions/HEAD/show) spark.

### Requirements

The GoCardless PHP client requires version 5.3 of PHP.

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
$ curl -L https://github.com/gocardless/gocardless-php/tarball/master | tar xzv
```

#### Download the Zip

[Click here](https://github.com/gocardless/gocardless-php/zipball/master)
to download the zip file.

#### Installing with Composer

Add `gocardless/gocardless` to the contents of your composer.json:

```javascript
{
    "require": {
        "gocardless/gocardless": ">=0.4.2"
    }
}
```

[![Build Status](https://secure.travis-ci.org/gocardless/gocardless-php.png?branch=master)](http://travis-ci.org/gocardless/gocardless-php)
