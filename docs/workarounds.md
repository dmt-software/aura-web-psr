# Workarounds

## Cookie and Set-Cookie headers

Cookies can be retrieved from the ServerRequests, but setting cookies on the response is not covered by this package. 
The original implementation can still manage the cookies, but if you want to change it a PSR-7 compatible solution like 
[dflydev/fig-cookies](https://packagist.org/packages/dflydev/fig-cookies) can be used to modernize the code.

## Redirects

The original redirect object retrieved from a response object can still function within the legacy code base, but using
it will break the immutability of the PSR-7 response message. (although it only changes the last received response).
To overcome this use a simple helper function/method to achieve the same result:
```php
use DMT\Aura\Psr\Message\Response;

function redirect(Response $response, $location, $code = '302', $phrase = '') {
    return $response
        ->withHeader('location', $location)
        ->withStatus($code, $phrase);
} 

// Usage:

/** @var Response $response */
$response = redirect($response, '/some-location', '302', 'Found');
```
One can use [rector/rector](https://packagist.org/packages/rector/rector) to replace any redirect calls with a PSR-7 
suitable solution. 
   
