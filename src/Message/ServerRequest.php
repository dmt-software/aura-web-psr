<?php

namespace DMT\Aura\Psr\Message;

use Aura\Web\Request as AuraRequest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequest
 *
 * @package DMT\Aura\Psr\Message
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /** @var UploadedFile[] $uploadedFiles */
    private $uploadedFiles;

    /**
     * ServerRequest constructor.
     *
     * @param string $method
     * @param string|UriInterface $uri
     * @param array $serverParams
     */
    public function __construct(string $method, $uri, array $serverParams = [])
    {
        parent::__construct($method, $uri);

        $request = $this->getInnerObject();
        $request->server->exchangeArray(['REQUEST_METHOD' => $method] + $serverParams);

        $this->setObjectProperty(
            $request ,
            'headers',
            new AuraRequest\Headers($request->server->get())
        );
    }

    /**
     * Retrieve server parameters.
     *
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->getInnerObject()->server->get();
    }

    /**
     * Retrieve cookies.
     *
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->getInnerObject()->cookies->get();
    }

    /**
     * Return an instance with the specified cookies.
     *
     * @param array $cookies
     * @return static
     */
    public function withCookieParams(array $cookies): self
    {
        $instance = clone($this);

        $this->setObjectProperty(
            $instance->getInnerObject(),
            'cookies',
            new AuraRequest\Values($cookies)
        );

        return $instance;
    }

    /**
     * Retrieve query string arguments.
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->getInnerObject()->query->get();
    }

    /**
     * Return an instance with the specified query string arguments.
     *
     * @param array $query
     * @return static
     */
    public function withQueryParams(array $query): self
    {
        $instance = clone($this);

        $this->setObjectProperty(
            $instance->getInnerObject(),
            'query',
            new AuraRequest\Values($query)
        );

        return $instance;
    }

    /**
     * Retrieve normalized file upload data.
     *
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles ?? [];
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * @param array|UploadedFileInterface[] $uploadedFiles
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withUploadedFiles(array $uploadedFiles): self
    {
        $files = [];
        $uploadedFiles = $this->checkUploadedFiles($uploadedFiles, $files);

        $instance = clone($this);
        $instance->uploadedFiles = $uploadedFiles;
        $instance->getInnerObject()->files->exchangeArray($files);

        return $instance;
    }

    public function checkUploadedFiles($uploadedFiles, &$entry)
    {
        foreach ($uploadedFiles as $file => &$uploadedFile) {
            if (is_array($uploadedFile)) {
                $uploadedFile = $this->checkUploadedFiles($uploadedFile, $entry[$file]);
                continue;
            }

            if (!$uploadedFile instanceof UploadedFileInterface) {
                throw new \InvalidArgumentException('illegal uploaded file entry');
            }

            if (!$uploadedFile instanceof UploadedFile) {
                $uploadedFile = new UploadedFile(
                    new Stream($uploadedFile->getStream()->detach()),
                    $uploadedFile->getSize(),
                    $uploadedFile->getError(),
                    $uploadedFile->getClientFilename(),
                    $uploadedFile->getClientMediaType()
                );
            }

            $entry[$file] = $uploadedFile->getInnerObject()->getArrayCopy();
        }

        return $uploadedFiles;
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * NOTE: objects are casted to arrays before storage
     *
     * @return null|array|object
     */
    public function getParsedBody()
    {
        $data = $this->getInnerObject()->post->get();

        if ((\ArrayObject::ARRAY_AS_PROPS & $this->getInnerObject()->post->getFlags()) > 0) {
            return (object)$data;
        }

        return $data ?: null;
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * @param null|array|object $data
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withParsedBody($data): self
    {
        if (!is_null($data) && !is_array($data) && !$data instanceof \stdClass) {
            throw new \InvalidArgumentException('unsupported body type used');
        }

        $instance = clone($this);
        $instance->getInnerObject()->post->exchangeArray((array)$data);

        if (is_object($data)) {
            $instance->getInnerObject()->post->setFlags(\ArrayObject::ARRAY_AS_PROPS);
        }

        return $instance;
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes(): array
    {
        return $this->getInnerObject()->params->get() ?? [];
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * @param string $name The attribute name.
     * @param mixed $default Default value to return if the attribute does not exist.
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->getInnerObject()->params->get($name, $default);
    }

    /**
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $value The value of the attribute.
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $params = $this->getAttributes();
        $params[$name] = $value;

        $instance = clone($this);
        $instance->getInnerObject()->params->set($params);

        return $instance;
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * @param string $name The attribute name.
     * @return static
     */
    public function withoutAttribute($name)
    {
        $params = $this->getAttributes();
        unset($params[$name]);

        $instance = clone($this);
        $instance->getInnerObject()->params->set($params);

        return $instance;
    }
}
