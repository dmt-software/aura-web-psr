# Aura Web PSR-7 wrapper

[![Latest Stable Version](https://poser.pugx.org/dmt-software/aura-web-psr/v/stable)](https://packagist.org/packages/dmt-software/aura-web-psr)
[![Build Status](https://travis-ci.com/dmt-software/aura-web-psr.svg?branch=master)](https://travis-ci.com/dmt-software/aura-web-psr)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dmt-software/aura-web-psr/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dmt-software/aura-web-psr/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dmt-software/aura-web-psr/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dmt-software/aura-web-psr/?branch=master)
[![License](https://poser.pugx.org/dmt-software/aura-web-psr/license)](https://packagist.org/packages/dmt-software/aura-web-psr)

## Introduction
Aura.Web implementations do not follow [PSR-7](https://www.php-fig.org/psr/psr-7/), the recommendation for HTTP 
messages. As more and more packages that solve common HTTP message problems do implement this recommendation, it would 
be nice if these can be used for Aura.Web implementations too. This package will allow you to start implementing PSR-7 
without changing the library underneath, preserving the current code usage <sup>[1](#1)</sup> to make migration or 
[refactoring](#usage-during-migration) easier.

## Installation

> Although Aura.Web still supports down to PHP 5.3, this package needs PHP 7.0 or higher. Older implementations need to 
> migrate to PHP 7 before this package can be used. I would suggest to use 
> [rector/rector](https://packagist.org/packages/rector/rector) to make this upgrade a more simple task.     
  

### Using composer

```composer require dmt-software/aura-web-psr```

## Usage

### Creating a ServerRequest

```php
use DMT\Aura\Psr\Message\ServerRequest;
 
// creating a request from $_SERVER variable
$serverRequest = new ServerRequest(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/',
    $_SERVER
);
```

### Handling uploaded files

```php
use DMT\Aura\Psr\Factory\UploadedFileFactory;
use DMT\Aura\Psr\Message\ServerRequest;
use DMT\Aura\Psr\Message\UploadedFile;
 
/** @var ServerRequest $serverRequest */
$serverRequest = $serverRequest->withUploadedFiles(
    /** @var UploadedFileFactory $uploadedFileFactory */
    $uploadedFileFactory->createUploadedFilesFromGlobalFiles($_FILES)
);
 
// at some later point 
foreach ($serverRequest->getUploadedFiles() as $uploadedFile) {
    /** @var UploadedFile $uploadedFile */
    if ($uploadedFile->getError() === \UPLOAD_ERR_OK) {
        // ... process the uploaded file
    }
}
```

### Creating a Response

```php
use DMT\Aura\Psr\Message\Response;
 
$response = new Response(200, 'Ok');
$response->getBody()->write(/** your response html */);
```

## Usage during migration 

### Wrapped objects

All PSR-7 http-messages wrap an Aura.Web object. According to their responsibility this can be any of the request or
response objects. These objects can be retrieved by calling the `getInnerObject()` method on the http-message.

```php 
use DMT\Aura\Psr\Message\ServerRequest;
 
$serverRequest = new ServerRequest(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/',
    $_SERVER
); 
 
$request = $serverRequest->getInnerObject();

// somewhere within the "legacy" code
if ($request->isPost()) {
    // process post data 
}
```

### Immutability

Changes to the http-messages will be internally tracked by the wrapped objects, but in such a way that
the immutability of the message is preserved. This means each change that is made to a message object will return a new 
Aura.Web object instance. 
```php
use DMT\Aura\Psr\Message\ServerRequest;
 
/** @var ServerRequest $serverRequest */ 
$auraRequest = $serverRequest->getInnerObject();
 
// new server request is returned with a fresh Aura.Web request 
$serverRequest = $serverRequest->withProtocolVersion('2');
$newAuraRequest = $serverRequest->getInnerObject();

if ($auraRequest->server->get('SERVER_PROTOCOL') != $newAuraRequest->server->get('SERVER_PROTOCOL')) {
    print 'Protocol version has changed';
}
```
> Make sure each time a http-message is changed, the aura request must be retrieved from the new message instance too.  
 
### Incompatibility

Some build in solutions, like uploading files, receiving a json post, etc are not available when this package is used.
For an overview of how to cope with these discrepancies see the [compatibility](/docs/compatibility.md) documentation.

That http-messages use (a part of) the Aura.Web objects does not mean it is true the other way around. Some aura objects
are not managed by the http-messages. For managing these objects one can fallback to the original code (once the inner
object is retrieved) or use/write some additional code that works similar. See the [workarounds](/docs/workarounds.md) 
documentation for tips and tricks on this subject.     



<a name="1"></a>
<sup>1: to the best of my ability</sup> 