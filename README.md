SMSGlobal WordPress Plugin
==========================
## Setup
The only requirement is the SMSGlobal REST API Client for PHP 5.2. There is a git submodule for it; otherwise you can get it [directly from GitHub](https://github.com/smsglobal/rest-api-client-php-5.2).

## Unit tests
Done via PHPUnit. The `test` shell script will do initial setup and testing:

    $ ./test

After the first run, you can use PHPUnit as normal:

    $ phpunit
