[![Build Status](https://travis-ci.org/smsglobal/rest-api-client-php-5.2.png?branch=master)](https://travis-ci.org/smsglobal/rest-api-client-php-5.2)

SMSGlobal Class Library for PHP 5.2
===================================
This is the version for PHP 5.2. The backport is generated automatically from [the PHP 5.3 version](https://github.com/smsglobal/rest-api-client-php).

Please use this only if you require PHP 5.2 compatibility. Otherwise, use [the PHP 5.3 version](https://github.com/smsglobal/rest-api-client-php).

This is a wrapper for the [SMSGlobal](http://www.smsglobal.com/) REST API. Get an API key from SMSGlobal by signing up and viewing the API key page in the MXT platform.

View the [REST API documentation](http://www.smsglobal.com/rest-api/) for a list of available resources.

Quick Start
-----------
This wrapper is requires PHP 5.2 or greater, and either the cURL library or the HTTP stream wrapper to be installed and enabled.

To install, clone or download this repository. See the example code below.

Running Unit Tests
------------------
```bash
$ cd path/to/SMSGlobal/rest/api/client
$ phpunit
```

Get documentation
-----------------
Documentation for the PHP 5.3 version is available on [the SMSGlobal website](http://www.smsglobal.com/docs/rest-api-client-php/). It is the same as the PHP 5.2 version but mentions namespaces instead of the pseudo namespaces (using _).

Using the library
-----------------
```php
// Register the autoloader, or configure your own
require dirname(__FILE__) . '/Smsglobal/Autoloader.php';
Smsglobal_Autoloader::register();

// Get an API key from SMSGlobal and insert the key and secret
$apiKey = new Smsglobal_RestApiClient_ApiKey('your-api-key', 'your-api-secret');

// All requests are done via a 'REST API client.' This abstracts away the REST
// API so you can deal with it like you would an ORM
$rest = new Smsglobal_RestApiClient_RestApiClient($apiKey);

// Now you can get objects
$contact = $rest->get('contact', 1); // Contact resource with ID = 1
// Edit them
$contact->setMsisdn('61447100250');
// And save them
$rest->save($contact);
// Or delete them
$rest->delete($contact);

// You can also instantiate new resources
$sms = new Smsglobal_RestApiClient_Resource_Sms();
$sms->setDestination('61447100250')
    ->setOrigin('Test')
    ->setMessage('Hello World');
// And save them
$rest->save($sms);
// When a new object is saved, the ID gets populated (it was null before)
echo $sms->getId(); // integer

// For an SMS, saving also sends the message, so you can use a more meaningful
// keyword for it
$sms->send($rest);

// You can get a list of available resources
$list = $rest->getList('sms');

foreach ($list->objects as $resource) {
    // ...
}

// Pagination data is included
echo $list->meta->getTotalPages(); // integer

// Lists can be filtered
// e.g. contacts belonging to group ID 1
$rest->getList('contact', 0, 20, array('group' => 1));
```

Notes
-----
1. Requesting the same object twice in one session will return the same instance (even in the resource lists)
2. Exceptions are thrown if you attempt to save an object with invalid data
