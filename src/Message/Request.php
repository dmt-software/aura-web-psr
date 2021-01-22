<?php

namespace DMT\Aura\Psr\Message;

use Aura\Web\Request as AuraRequest;
use Aura\Web\WebFactory;
use DMT\Aura\Psr\Factory\UriFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 *
 * @package DMT\Aura\Psr
 */
class Request implements RequestInterface
{
    use MessageTrait;

    /** @var AuraRequest $request */
    protected $request;
    /** @var UriInterface $uri */
    protected $uri;

    /**
     * Request constructor.
     *
     * @param AuraRequest $request The Aura\Web\Request to wrap
     */
    public function __construct(AuraRequest $request)
    {
        $this->request = $request;
        $this->getUri();
    }

    /**
     * @return AuraRequest
     */
    public function getInnerObject(): AuraRequest
    {
        if (!$this->request) {
            $this->request = (new WebFactory([]))->newRequest();
        }

        return $this->request;
    }

    /**
     * Retrieves the message's request target.
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        return $this->getInnerObject()->server->get('REQUEST_URI', '/');
    }

    /**
     * Return an instance with the specific request-target.
     *
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget): self
    {
        $server = $this->getInnerObject()->server->get();
        $server['REQUEST_URI'] = $requestTarget;
        $server = new AuraRequest\Values($server);

        return $this->newInstanceWith(compact('server'));
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string
    {
        return $this->getInnerObject()->method->get() ?: 'GET';
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method): self
    {
        if (!is_string($method)) {
            throw new \InvalidArgumentException('Invalid method given');
        }

        return $this->newInstanceWith([
            'method' => new AuraRequest\Method(['REQUEST_METHOD' => $method], [])
        ]);
    }

    /**
     * Retrieves the URI instance.
     *
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        if (!$this->uri instanceof Uri) {
            $this->uri = new Uri($this->getInnerObject()->url);
        }

        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * @param UriInterface $uri
     * @param bool $preserveHost
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        if (!$uri instanceof Uri) {
            $uri = (new UriFactory())->createUri((string)$uri);
        }

        $request = $this->newInstanceWith(['url' => $uri->getInnerObject()]);
        $request->uri = $uri;

        if (!$preserveHost || !$this->hasHeader('host')) {
            return $request->updateHostFromUri();
        }

        return $request;
    }

    /**
     * Update the host whilst setting a new URI.
     *
     * @return static
     */
    protected function updateHostFromUri(): self
    {
        $host = $this->uri->getHost();
        $port = $this->uri->getPort();

        if ($host === '') {
            return $this;
        }

        if ($port !== null) {
            $host .= ':' . $port;
        }

        return $this->withHeader('host', $host);
    }

    /**
     * Get the object to override that contains the protocol version.
     *
     * @param string $version
     * @return array
     */
    protected function getProtocolVersionContainer(string $version): array
    {
        $server = $this->getInnerObject()->server->get();
        $server['SERVER_PROTOCOL'] = 'HTTP/' . $version;
        $server = new AuraRequest\Values($server);

        return compact('server');
    }

    /**
     * Get the header container.
     *
     * @param array $headerValues
     * @return array
     */
    protected function getHeaderContainer(array $headerValues = []): array
    {
        $headers = [];
        array_walk($headerValues, function ($values, $header) use (&$headers) {
            if ($values !== null) {
                $headers[$this->originalHeaderName($header)] = implode(',', $this->normalizeHeaderValue($values));
            }
        });

        return ['headers' => new AuraRequest\Headers($headers)];
    }

    /**
     * Ensure the immutability of the request.
     *
     * @param array $override
     * @return static
     */
    protected function newInstanceWith(array $override = []): self
    {
        $innerRequest = $this->getInnerObject();

        $newInstance = clone($this);
        $newInstance->request = new AuraRequest(
            $override['client'] ?? clone($innerRequest->client),
            $override['content'] ?? clone($innerRequest->content),
            new AuraRequest\Globals(
                $override['cookies'] ?? clone($innerRequest->cookies),
                $override['env'] ?? clone($innerRequest->env),
                $override['files'] ?? clone($innerRequest->files),
                $override['post'] ?? clone($innerRequest->post),
                $override['query'] ?? clone($innerRequest->query),
                $override['server'] ?? clone($innerRequest->server)
            ),
            $override['headers'] ?? clone($innerRequest->headers),
            $override['method'] ?? clone($innerRequest->method),
            clone($innerRequest->params),
            $override['url'] ?? clone($innerRequest->url)
        );

        return $newInstance;
    }
}
