<?php

namespace DMT\Aura\Psr;

use Aura\Web\Request as AuraRequest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    /** @var UploadedFile[] $uploadedFiles */
    private $uploadedFiles;

    /**
     * Request constructor.
     * @param AuraRequest $request The Aura\Web\Request to wrap
     */
    public function __construct(AuraRequest $request)
    {
        parent::__construct($request);

        $format = '~(application/x\-www\-form\-urlencoded|multipart/form\-data)~i';
        $contentType = $this->getHeader('Content-Type');
        if (!preg_grep($format, $contentType) && $request->content->getType() && empty($request->post->get())) {
            $contents = clone($request->content);
            $body = $contents->get();
            if (is_array($body) || $body instanceof \stdClass) {
                $request->post->exchangeArray((array) $body);
            }
        }

        $this->getUploadedFiles();
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
        return $this->newInstanceWith([
            'cookies' => new AuraRequest\Values($cookies)
        ]);
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
        return $this->newInstanceWith([
            'query' => new AuraRequest\Values($query)
        ]);
    }

    /**
     * Retrieve normalized file upload data.
     *
     * @return array
     */
    public function getUploadedFiles(): array
    {
        if (!$this->uploadedFiles) {
            $this->uploadedFiles = array_map(
                function ($uploadedFile) {
                    return new UploadedFile(new AuraRequest\Values($uploadedFile));
                },
                array_values($this->getInnerObject()->files->get())
            );
        }

        return $this->uploadedFiles;
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
        foreach ($uploadedFiles as $uploadedFile) {
            if (!$uploadedFile instanceof UploadedFileInterface) {
                throw new \InvalidArgumentException('illegal uploaded file entry');
            }

            $files[$uploadedFile->getClientFilename()] = [
                'error' => $uploadedFile->getError(),
                'name' => $uploadedFile->getClientFilename(),
                'size' => $uploadedFile->getSize(),
                'tmp_name' => $uploadedFile->getStream()->getMetadata('uri'),
                'type' => $uploadedFile->getClientMediaType(),
            ];
        }

        $request = $this->newInstanceWith(['files' => new AuraRequest\Files($files)]);
        $request->uploadedFiles = $uploadedFiles;

        return $request;
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

        if (array_key_exists('__object__', $data)) {
            $data = $data['__object__'];
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
        if (!is_null($data) && !is_array($data) && !is_object($data)) {
            throw new \InvalidArgumentException('unsupported body type used');
        }

        if (is_object($data)) {
            $data = ['__object__' => $data];
        }

        $post = new AuraRequest\Values((array)$data);

        return $this->newInstanceWith(compact('post'));
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

        $request = $this->newInstanceWith();
        $request->getInnerObject()->params->set($params);

        return $request;
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

        $request = $this->newInstanceWith();
        $request->getInnerObject()->params->set($params);

        return $request;
    }
}
