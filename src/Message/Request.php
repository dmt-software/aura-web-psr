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
 * @package DMT\Aura\Psr\Message
 */
class Request implements RequestInterface
{
    use MessageTrait;

    /** @var AuraRequest $object */
    protected $object;
    /** @var UriInterface $uri */
    protected $uri;

    /**
     * Request constructor.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request.
     */
    public function __construct($method, $uri)
    {
        $request = $this->getInnerObject();
        $server = $request->server;
        $server['REQUEST_METHOD'] = $method;

        if ((string)$uri !== '' && $components = parse_url($uri)) {
            $this->setObjectProperty($request->url, 'parts', $components);
        }
        $this->uri = new Uri($request->url);
    }

    /**
     * @return AuraRequest
     */
    public function getInnerObject(): AuraRequest
    {
        if (!$this->object) {
            $this->object = (new WebFactory([]))->newRequest();
        }

        return $this->object;
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
        $instance = clone($this);

        $server = $instance->getInnerObject()->server;
        $server['REQUEST_URI'] = $requestTarget;

        return $instance;
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

        $instance = clone($this);
        $instance->setObjectProperty($instance->getInnerObject()->method, 'value', $method);

        return $instance;
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

        $instance = clone($this);
        $instance->uri = $uri;
        $instance->setObjectProperty($instance->getInnerObject(), 'url', $uri->getInnerObject());

        if (!$preserveHost || !$this->hasHeader('host')) {
            return $instance->updateHostFromUri();
        }

        return $instance;
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
}
