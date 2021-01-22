<?php

namespace DMT\Aura\psr\Message;

use Aura\Web\Request\Values;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    /** @var Values $file */
    private $file;
    /** @var StreamInterface $stream */
    private $stream;

    /**
     * UploadedFile constructor.
     *
     * @param Values $file
     */
    public function __construct(Values $file)
    {
        $this->file = $file;

        if ($this->getError() === UPLOAD_ERR_OK) {
            $this->stream = new Stream($file);
        }
    }

    /**
     * Get the inner object.
     *
     * @return Values
     */
    public function getInnerObject(): Values
    {
        return $this->file;
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * @return StreamInterface
     * @throws \RuntimeException
     */
    public function getStream(): StreamInterface
    {
        $this->validateStream();

        return $this->stream;
    }

    /**
     * Move the uploaded file to a new location.
     *
     * @param string $targetPath
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function moveTo($targetPath): void
    {
        if (!is_string($targetPath) || $targetPath === '') {
            throw new \InvalidArgumentException('invalid target path');
        }

        $target = fopen($targetPath, 'w');
        if ($target === false) {
            throw new \RuntimeException('target is not writeable');
        }

        $stream = $this->getStream();
        $stream->rewind();

        while (!$stream->eof()) {
            if (fwrite($target, $stream->read(8196)) === false) {
                throw new \RuntimeException('error whilst writing to target');
            }
        }

        $stream->close();
    }

    /**
     * Retrieve the file size.
     *
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->file->get('size');
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * @return int
     */
    public function getError(): int
    {
        return $this->file->get('error', UPLOAD_ERR_OK);
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * @return string|null
     */
    public function getClientFilename(): ?string
    {
        return $this->file->get('name');
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * @return string|null
     */
    public function getClientMediaType(): ?string
    {
        return $this->file->get('type');
    }

    /**
     * Validate the stream.
     *
     * @throws \RuntimeException
     */
    private function validateStream(): void
    {
        if ($this->getError() !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('file contains errors');
        }

        if (!$this->stream->isReadable()) {
            throw new \RuntimeException('file cannot be read');
        }
    }
}
