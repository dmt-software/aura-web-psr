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
refactoring easier.

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
    $uploadedFileFactory->createUploadedFilesFromGlobalFiles($_FILES);
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


<a name="1"></a>
<sup>1: to the best of my ability</sup> 