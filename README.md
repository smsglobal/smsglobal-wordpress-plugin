SMSGlobal WordPress Plugin
==========================
## Setup
The only requirement is the SMSGlobal REST API Client for PHP 5.2. There is a git submodule for it; otherwise you can get it [directly from GitHub](https://github.com/smsglobal/rest-api-client-php-5.2). Install it to the `vendor/rest-api-client-php-5.2` directory.

With version 1.6.5 of git and later, you can use:

```
git clone --recursive git://github.com/smsglobal/smsglobal-wordpress-plugin smsglobal
```

For older versions, use:

```
git clone git://github.com/smsglobal/smsglobal-wordpress-plugin smsglobal
cd smsglobal
git submodule init
git submodule update
```

## Unit tests
Done via PHPUnit. The `test` shell script will do initial setup and testing:

    $ ./test

After the first run, you can use PHPUnit as normal:

    $ phpunit
