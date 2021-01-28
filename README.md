# Aura Web PSR-7 wrapper

## Introduction
Aura.Web implementations do not follow [PSR-7](https://www.php-fig.org/psr/psr-7/), the recommendation for HTTP 
messages. As more and more packages that solve common HTTP message problems do implement this recommendation, it would 
be nice if these can be used for Aura.Web implementations too. This package will allow you to start implementing PSR-7 
without changing the library underneath, preserving the current code usage <sup>[1]</sup> to make migration or 
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


<a name=1></a>
<sup>1: to the best of my ability</sup> 