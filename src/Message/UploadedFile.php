<?php

namespace DMT\Aura\Psr\Message;

use Aura\Web\Request\Values;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use const UPLOAD_ERR_OK;

/**
 * Class UploadedFile
 *
 * @package DMT\Aura\Psr\Message
 */
class UploadedFile implements UploadedFileInterface
{
    /** @var StreamInterface $stream */
    private $stream;
    /** @var Values $file */
    private $file;

    /**
     * UploadedFile constructor.
     *
     * @param StreamInterface $stream
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function __construct(
        StreamInterface $stream,
        int $size = null,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ) {
        $this->stream = $stream;
        $this->file = new Values([
            'error' => $error,
            'name' => $clientFilename,
            'size' => $size,
            'tmp_name' => $stream->getMetadata('uri'),
            'type' => $clientMediaType,
        ]);
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
     * @throws RuntimeException
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
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function moveTo($targetPath)
    {
        if (!is_string($targetPath) || $targetPath === '') {
            throw new InvalidArgumentException('invalid target path');
        }

        $target = fopen($targetPath, 'w');
        if ($target === false) {
            throw new RuntimeException('target is not writeable');
        }

        $stream = $this->getStream();
        $stream->rewind();

        while (!$stream->eof()) {
            if (fwrite($target, $stream->read(8196)) === false) {
                throw new RuntimeException('error whilst writing to target');
            }
        }

        $stream->close();
    }

    /**
     * Retrieve the file size.
     *
     * @return int|null
     */
    public function getSize()
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
    public function getClientFilename()
    {
        return $this->file->get('name');
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * @return string|null
     */
    public function getClientMediaType()
    {
        return $this->file->get('type');
    }

    /**
     * Validate the stream.
     *
     * @throws RuntimeException
     */
    private function validateStream()
    {
        if ($this->getError() !== UPLOAD_ERR_OK) {
            throw new RuntimeException('file contains errors');
        }

        if (!$this->stream->isReadable()) {
            throw new RuntimeException('file cannot be read');
        }
    }
}
