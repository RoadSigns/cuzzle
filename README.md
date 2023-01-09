# Cuzzle, cURL command from Guzzle requests

This library lets you dump a Guzzle request to a cURL command for debug and log purpose.

## Prerequisites

This library needs PHP 8.0+

## Installation

You can install the library directly with composer:

```
composer require RoadSigns/cuzzle
```

(Add `--dev` if you don't need it in production environment)

## Usage

```php

use RoadSigns\Cuzzle\Formatter\CurlFormatter;
use GuzzleHttp\Message\Request;

$request = new Request('GET', 'example.local');
$options = [];

echo (new CurlFormatter())->format($request, $options);

```

To log the cURL request generated from a Guzzle request, simply add CurlFormatterSubscriber to Guzzle:

```php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use RoadSigns\Cuzzle\Middleware\CurlFormatterMiddleware;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

$logger = new Logger('guzzle.to.curl'); //initialize the logger
$testHandler = new TestHandler(); //test logger handler
$logger->pushHandler($testHandler);

$handler = HandlerStack::create();
$handler->after('cookies', new CurlFormatterMiddleware($logger)); //add the cURL formatter middleware
$client  = new Client(['handler' => $handler]); //initialize a Guzzle client

$response = $client->get('http://google.com'); //let's fire a request

var_dump($testHandler->getRecords()); //check the cURL request in the logs,
//you should see something like: "curl 'http://google.com' -H 'User-Agent: Guzzle/4.2.1 curl/7.37.1 PHP/5.5.16"

```

## Tests

You can run tests locally with

```
phpunit
```

## Original

This package is a fork from https://github.com/namshi/cuzzle
This is to allow people to use this package with Guzzle >= 7.0

Some fundamental changes have been made to the code so a simple port across might not work exactly.

We are now returning a `Curl` class that is `Stringable` instead of a string.
This allows us to move all the logic to create the cURL out of the Request formatter.

## Feedback

Add an issue, open a PR, drop us a message!