# Stooge
A PHP library for simplifying the process of making HTTP requests via cURL.

## Download / Install
The easiest way to install Stooge is via Composer:
```bash
composer require "tagadvance/trapdoor:dev-master"
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
print $response;
```