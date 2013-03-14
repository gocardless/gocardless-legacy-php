[![GoCardless PHP Client Library](https://s3-eu-west-1.amazonaws.com/gocardless/images/client-lib-headers/php-lib-header.png)](https://gocardless.com/docs?language=php)

The GoCardless PHP client provides a simple PHP interface to the GoCardless
API.

The following links may be useful:

- [Developer overview](http://blog.gocardless.com/post/19695292096/goingcardless-an-introduction-to-gocardless-for) of GoCardless
- [Documentation](https://gocardless.com/docs/php/merchant_client_guide) for individual merchants
- [Documentation](https://gocardless.com/docs/php/partner_client_guide) for [partners](http://blog.gocardless.com/post/19743008707/goingcardless-our-partner-system-explained) (multiple merchants)
- Our [introductory guide](http://blog.gocardless.com/post/17945439079/gocardless-php-library) to using the PHP library
- Some more [advanced PHP library usage](http://blog.gocardless.com/post/17945439079/gocardless-php-library)
- [Code samples](https://github.com/gocardless/gocardless-php/tree/master/examples)
- Our CodeIgniter [plugin](https://github.com/gocardless/codeigniter-gocardless) and [spark](http://getsparks.org/packages/GoCardless/versions/HEAD/show)
- You can also use GoCardless via the [PHP Payments](https://github.com/calvinfroedge/PHP-Payments) library and [CodeIgniter Payments](http://getsparks.org/packages/codeigniter-payments/versions/HEAD/show) spark
- [Full library reference](http://gocardless.github.com/gocardless-php/)
- Our developer support [Campfire chat room](https://gocardless.campfirenow.com/3ae88)

### Installation

The files you need to use the GoCardless API are in the /lib folder.

#### Install from source

```console
$ git clone git://github.com/gocardless/gocardless-php.git
```

#### Installing from the tarball

```console
$ curl -L https://github.com/downloads/gocardless/gocardless-php/gocardless-php-v0.3.3.tgz | tar xzv
```

#### Download the Zip

[Click here](https://github.com/gocardless/gocardless-php/zipball/v0.3.3)
to download the zip file.

#### Installing with Composer

Add `gocardless/gocardless` to the contents of your composer.json:

```javascript
{
    "require": {
        "gocardless/gocardless": ">=0.3.3"
    }
}
```

[![Build Status](https://secure.travis-ci.org/gocardless/gocardless-php.png?branch=master)](http://travis-ci.org/gocardless/gocardless-php)
