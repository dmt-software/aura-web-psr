<?php

namespace DMT\Aura\Psr\Message;

use Aura\Web\Request as AuraRequest;
use Aura\Web\Response as AuraResponse;
use Psr\Http\Message\StreamInterface;

/**
 * Trait MessageTrait
 *
 * @package DMT\Aura\Psr
 */
trait MessageTrait
{
    /** @var StreamInterface $body */
    private $body;

    /**
     * @return AuraRequest|AuraResponse
     */
    abstract public function getInnerObject();

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        $version = '';

        $innerObject = $this->getInnerObject();
        if ($innerObject instanceof AuraRequest) {
            $version = str_replace('HTTP/', '', $innerObject->server->get('SERVER_PROTOCOL', '1.1'));
        } elseif ($innerObject instanceof AuraResponse) {
            $version = $innerObject->status->getVersion();
        }

        return $version;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * @param string $version HTTP protocol version
     * @return static
     */
    public function withProtocolVersion($version): self
    {
        if (is_bool($version) || !is_scalar($version) || !preg_match('~^(1(\.[01])|2(\.0)?)$~', $version)) {
            return $this->newInstanceWith();
        }

        return $this->newInstanceWith($this->getProtocolVersionContainer($version));
    }

    /**
     * Retrieves all message header values.
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders(): array
    {
        $setHeaders = $this->getInnerObject()->headers->get();

        $headers = [];
        array_walk($setHeaders, function ($value, $key) use (&$headers) {
            $headers[$this->originalHeaderName($key)] = $this->normalizeHeaderValue($value);
        });

        return $headers;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name): bool
    {
        if (empty($name) || !is_string($name)) {
            return false;
        }

        if ($this->getInnerObject() instanceof AuraResponse) {
            return null !== $this->getInnerObject()->headers->get($name);
        }

        return array_key_exists($this->originalHeaderName($name), $this->getHeaders());
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name): array
    {
        $value = [];

        $innerObject = $this->getInnerObject();
        if ($innerObject instanceof AuraRequest) {
            $value = $this->getInnerObject()->headers->get($this->normalizeHeaderName($name));
        } elseif ($innerObject instanceof AuraResponse) {
            $value = $innerObject->headers->get($name);
        }

        return $this->normalizeHeaderValue($value);
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withHeader($name, $value): self
    {
        if (!is_string($name) || $name === '') {
            throw new \InvalidArgumentException('invalid header name');
        }

        if ((!is_string($value) && !is_array($value)) || $value === []) {
            throw new \InvalidArgumentException('invalid header value');
        }

        $headers = $this->getHeaders();
        $headers[$name] = $value;

        return $this->newInstanceWith($this->getHeaderContainer($headers));
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value): self
    {
        if ($this->hasHeader($name)) {
            $value = array_merge($this->getHeader($name), (array)$value);
        }

        return $this->withHeader($name, $value);
    }

    /**
     * Return an instance without the specified header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function withoutHeader($name): self
    {
        $headers = $this->getHeaders();
        if ($this->hasHeader($name)) {
            $headers[$this->originalHeaderName($name)] = null;
        }

        return $this->newInstanceWith($this->getHeaderContainer($headers));
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        if (!$this->body instanceof Stream) {
            $this->body = new Stream($this->getInnerObject()->content);
        }

        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * @param StreamInterface $body
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withBody(StreamInterface $body): self
    {
        if (!$body instanceof Stream) {
            $contentClass = get_class($this->getInnerObject()) . '\\Content';
            $body = new Stream(new $contentClass($this->getHeaders()), $body->detach());
        }

        $content = $body->getInnerObject();

        $object = $this->newInstanceWith(compact('content'));
        $object->body = $body;

        return $object;
    }

    /**
     * Get the object to override that contains the protocol version.
     *
     * @param string $version
     * @return array
     */
    abstract protected function getProtocolVersionContainer(string $version): array;

    /**
     * @param array $override
     * @return static
     */
    abstract protected function newInstanceWith(array $override = []): self;

    /**
     * Get the original header name.
     *
     * @param string|mixed $name
     * @return string
     * @throws \InvalidArgumentException
     */
    private function originalHeaderName($name): string
    {
        if (!is_string($name) || $name === '') {
            throw new \InvalidArgumentException('header name must be a string');
        }

        if ($this->getInnerObject() instanceof AuraResponse) {
            return $name;
        }

        if (stripos($name, 'content') !== 0 && stripos($name, 'http_') !== 0) {
            $name = 'http_' . $name;
        }

        return str_replace('-', '_', strtoupper($name));
    }

    /**
     * Normalize the header name.
     *
     * @param string $name
     * @return string
     */
    private function normalizeHeaderName(string $name): string
    {
        return str_replace(['http', '_'], ['', '-'], strtolower($name));
    }

    /**
     * Normalize the header value.
     *
     * @param null|string|array $value
     * @return array
     */
    private function normalizeHeaderValue($value): array
    {
        if (null === $value) {
            return [];
        }

        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (!is_array($value) || empty($value)) {
            throw new \InvalidArgumentException('invalid header value');
        }

        return $value;
    }
}
