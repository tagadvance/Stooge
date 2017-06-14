# Stooge
A PHP library for simplifying the process of making HTTP requests via cURL.

Stooge performs HTTP requests by using `CurlRequest` as a fluent builder for PHP's [Client URL Library](http://php.net/manual/en/book.curl.php).
* `__invoke()` has been overridden to call `execute()`
* there are convenience methods for `get`, `post`, and `put` operations
* `autoCookieJar()` is useful if sessions are necessary

## Download / Install
The easiest way to install Stooge is via Composer:
```bash
composer require "tagadvance/stooge:dev-master"
```
```json
{
    "require": {
        "tagadvance/stooge": "dev-master"
    }
}
```

## Example
```php
<?php

use tagadvance\stooge\CurlRequest;

require_once 'vendor/autoload.php';

$break = str_pad ( $input = '', $pad_length = 5, PHP_EOL );

$url = 'http://intentionallyblankpage.com';
// $url = 'http://intentionallyblankpage.com/redirect.php';
// $url = 'http://intentionallyblankpage.com/test.html';

$request = new CurlRequest ();
$request->autoDetectUserAgent ();
$response = $request
		->setAutoreferer ()
		->setReturntransfer ()
		->setConnecttimeout ( 30 )
		->setTimeout ( 30 )
		->setFollowlocation ()
		->setMaxredirs ( 3 )
		->setFreshConnect ()
		->setForbidReuse ()
		->get ( $url );
print $response . $break;

$url = 'http://intentionallyblankpage.com/test.php';
$response = $request->setUrl ( $url )->post ( $fields = [ ] );
print $response . $break;

$response = $request->setUrl ( $url )->put ( $fields = [ ] );
print $response . PHP_EOL;
```

## What's with the name?
[cURL](https://curl.haxx.se/) -> [Curly Howard](https://en.wikipedia.org/wiki/Curly_Howard) -> [The Three Stooges](https://en.wikipedia.org/wiki/The_Three_Stooges) -> [Stooge](https://github.com/tagadvance/Stooge)