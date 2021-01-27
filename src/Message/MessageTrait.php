<?php

namespace DMT\Aura\Psr\Message;

use Aura\Web\Request as AuraRequest;
use Aura\Web\Response as AuraResponse;
use DMT\Aura\Psr\Helpers\HelperFactory;
use Psr\Http\Message\StreamInterface;

/**
 * Trait MessageTrait
 *
 * @package DMT\Aura\Psr\Message
 */
trait MessageTrait
{
    /** @var StreamInterface $body */
    private $body;
    /** @var AuraRequest|AuraResponse $object */
    private $object;

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
        $instance = clone($this);

        $innerObject = $instance->getInnerObject();
        if ($innerObject instanceof AuraRequest) {
            $server = $innerObject->server;
            $server['SERVER_PROTOCOL'] = 'HTTP/' . $version;
        } elseif ($innerObject instanceof AuraResponse) {
            $instance->setObjectProperty($innerObject->status, 'version', $version);
        }

        return $instance;
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
        return $this->getInnerObject()->headers->get();
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
        if (empty($name) || !is_string($name) || !preg_match('~^[a-z\-]+$~i', $name)) {
            return false;
        }

        return !!preg_grep("~^{$name}$~i", array_keys($this->getHeaders()));
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
        $headers = $this->getHeaders();
        if ($key = current(preg_grep("~^" . preg_quote($name) . "$~i", array_keys($headers)))) {
            $name = $key;
        }

        if (!array_key_exists($name, $headers)) {
            return [];
        }

        return $this->normalizeHeaderValue($headers[$name]);
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
     * Return an instance with the provided value replacing the specified header.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withHeader($name, $value): self
    {
        if (!is_string($name) || $name === '' || !preg_match('~^[a-z\-]+$~i', $name)) {
            throw new \InvalidArgumentException('invalid header name');
        }

        if ((!is_string($value) && !is_array($value)) || $value === []) {
            throw new \InvalidArgumentException('invalid header value');
        }

        $headers = $this->getHeaders();
        if ($key = current(preg_grep("~^{$name}$~i", array_keys($headers)))) {
           unset($headers[$key]);
        }
        $headers[$name] = $value;

        $instance = clone($this);
        $this->setObjectProperty(
            $instance->getInnerObject()->headers,
            $this->getInnerObject() instanceof AuraResponse ? 'headers' : 'data',
            $headers
        );

        return $instance;
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
            $value = array_merge_recursive(array_values($this->getHeader($name)), (array)$value);
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
        if (!is_string($name) || $name === '' || !preg_match('~[a-z\-]~i', $name)) {
            throw new \InvalidArgumentException('invalid header name');
        }

        $headers = $this->getHeaders();
        if ($key = current(preg_grep("~^{$name}$~i", array_keys($headers)))) {
            $name = $key;
        }
        unset($headers[$name]);

        $instance = clone($this);
        $this->setObjectProperty(
            $instance->getInnerObject()->headers,
            $this->getInnerObject() instanceof AuraResponse ? 'headers' : 'data',
            $headers
        );

        return $instance;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
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
            $body = new Stream($body->detach());
        }

        $instance = clone($this);
        $instance->body = $body;

        $object = $instance->getInnerObject();
        if ($object instanceof AuraRequest) {
            $this->setObjectProperty($object->content, 'raw', $body);
        } elseif ($object instanceof AuraResponse) {
            $object->content->set($body);
        }

        return $instance;
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

    public function __clone()
    {
        $this->object = (new HelperFactory())
            ->createHelper($this->getInnerObject())
            ->cloneObject();
    }

    protected function setObjectProperty($object, $property, $value)
    {
        (new HelperFactory())
            ->createHelper($object)
            ->setObjectProperty($property, $value);
    }
}
