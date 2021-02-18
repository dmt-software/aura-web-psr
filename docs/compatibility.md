# Compatibility

## Recreate Aura behavior

> NOTE: Some examples implements middleware described in [PSR-15](https://www.php-fig.org/psr/psr-15/)

### Request Method

Aura has a way of overriding the request method by adding a hidden form element named `_method`. This functionality is
lost when you start using this package. This can be recreated.
```php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
 
class OverrideRequestMethodMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (strtoupper($request->getMethod()) === 'POST') {
            $postData = $request->getParsedBody();
            if (array_key_exists('_method', $postData)) { 
                $request = $request->withMethod($postData['_method']);
            }
        }
        
        return $handler->handle($request);
    }
}
```

### Posted JSON

Aura can parse json inside the request content using a decoder. With the ServerRequest we can achieve something similar
using the `withParsedBody`.
```php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
 
class OverrideRequestMethodMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (strpos($request->getHeaderLine('Content-Type'), 'application/json') !== false) {
            $content = file_get_contents('php://input');
            $parsed = @json_decode($content);
            
            if (!empty($parsed)) {
                $request = $request->withParsedBody($parsed);
            }
        }
        
        return $handler->handle($request);
    }
} 
```


## Migration discrepancies

### Headers

> Headers added might not be accessible by the legacy code as expected.

Request and response headers in PSR-7 compatible http messages preserve case while Aura.Web formats the header name
before storing them. To overcome this difference a workaround is in place. This workaround might cause some problems
during migration. See the [workaround documentation](/docs/workarounds.md#headers) how this can be tackled.

### Content

The response content is stored as a stream, not as a string. This stream is "printable". When the `ResponseSender` is
used to present the response output this will not cause any problems. If for some other reason the response is handled 
different, make sure the content is cast to string. 

### Files

The recreated request files object might differ from the one that is created be the original aura web factory. This 
depends on the form elements that are posted. 
```html
<!-- unaffected -->
<input name="image">
<input name="documents[]" multiple>
<!-- not fully normalized -->
<input name="app[user][image]">
<input name="user[image][]" multiple>
```
An [issue](https://github.com/auraphp/Aura.Web/issues/57) is posted to request the Aura.Web's maintainers to resolve
this, as it is beneficial to all users.