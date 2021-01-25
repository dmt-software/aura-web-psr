<?php

namespace DMT\Aura\Psr\Message;

use Aura\Web\Request\Content as RequestContent;
use Aura\Web\Request\Values;
use Aura\Web\Response\Content as ResponseContent;
use Psr\Http\Message\StreamInterface;

/**
 * Class Stream
 *
 * @package DMT\Aura\Psr\Message
 */
class Stream implements StreamInterface
{
    /** @var RequestContent */
    private $original;
    /** @var resource $stream */
    private $stream;
    /** @var RequestContent */
    private $contents;
    /** @var array $metadata */
    private $metadata;

    /**
     * Stream constructor.
     *
     * @param RequestContent|ResponseContent|Values $contents
     * @param resource|null $stream
     */
    public function __construct($contents, $stream = null)
    {
        if ($contents instanceof RequestContent) {
            $this->forRequest($contents, $stream ?? fopen('data://text/plain,' . $contents->getRaw(), 'r+'));
        } elseif ($contents instanceof Values) {
            $this->contents = $contents;
            $this->stream = $stream ?? fopen($contents->get('tmp_name'), 'rb');
        } elseif ($contents instanceof ResponseContent) {
            $this->forResponse($contents, $stream ?? fopen('data://text/plain,' . $contents->get(), 'r+'));
        }

        $this->original = $contents;
    }

    /**
     * @return RequestContent|Values
     */
    public function getInnerObject()
    {
        $this->contents->get();

        return $this->original;
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (\RuntimeException $exception) {
            return '';
        } finally {
            $this->contents->get();
        }
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getContents()
    {
        if (!is_resource($this->stream)) {
            throw new \RuntimeException('could not read stream');
        }

        $contents = stream_get_contents($this->stream);
        if ($contents === false) {
            throw new \RuntimeException('error whilst reading stream');
        }

        return $contents;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * @return resource|null
     */
    public function detach()
    {
        if (!is_resource($this->stream)) {
            return null;
        }

        $handle = $this->stream;
        $this->close();

        return $handle;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null
     */
    public function getSize()
    {
        if (!is_resource($this->stream)) {
            return null;
        }

        return fstat($this->stream)['size'] ?? null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException
     */
    public function tell()
    {
        if (!is_resource($this->stream)) {
            throw new \RuntimeException('could not read stream');
        }

        $result = ftell($this->stream);
        if ($result === false) {
            throw new \RuntimeException('could not determine stream position');
        }

        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return is_resource($this->stream) ? feof($this->stream) : true;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->getMetadata('seekable');
    }

    /**
     * Seek to a position in the stream.
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated based on the seek offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!is_resource($this->stream)) {
            throw new \RuntimeException('could not read stream');
        }

        if (!$this->isSeekable()) {
            throw new \RuntimeException('stream is not seekable');
        }

        if (fseek($this->stream, $offset, (int)$whence) === -1) {
            throw new \RuntimeException('unable to seek stream to position');
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * @throws \RuntimeException
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return preg_match('~([acwx]|r(?=b?[\+w]))~', $this->getMetadata('mode')) > 0;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('cannot write to stream');
        }

        $bytes = fwrite($this->stream, $string);
        if ($bytes === false) {
            throw new \RuntimeException('error whilst writing to stream');
        }

        return $bytes;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return preg_match('~(rw?|[acwx](?=b?\+))~', $this->getMetadata('mode')) > 0;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length
     * @return string
     * @throws \RuntimeException
     */
    public function read($length)
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('could not read from stream');
        }

        if ((int)$length < 1) {
            return '';
        }

        $part = fread($this->stream, (int)$length);
        if ($part === false) {
            throw new \RuntimeException('error whilst reading the stream');
        }

        return $part;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null
     */
    public function getMetadata($key = null)
    {
        if (!is_resource($this->stream)) {
            return $key === null ? [] : null;
        }

        if (empty($this->metadata)) {
            $this->metadata = stream_get_meta_data($this->stream);
        }

        if ($key === null) {
            return $this->metadata;
        }

        if (!is_string($key)) {
            return null;
        }

        return $this->metadata[$key] ?? null;
    }

    /**
     * Close the internal handle.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param RequestContent $contents
     * @param resource $stream
     */
    private function forRequest(RequestContent $contents, $stream): void
    {
        if (!is_resource($stream)) {
            throw new \TypeError('Stream is expected to be a resource');
        }

        $server = ['CONTENT_TYPE' => 'text/plain'];
        $decoders = ['text/plain' => function () use (&$contents) {
            $raw = new \ReflectionProperty(RequestContent::class, 'raw');
            $raw->setAccessible(true);
            $raw->setValue($contents, (string)$this);
        }];

        $this->contents = new RequestContent($server, $decoders);
        $this->original = $contents;
        $this->stream = $stream;
    }

    /**
     * @param ResponseContent $contents
     * @param resource $stream
     */
    private function forResponse(ResponseContent $contents, $stream): void
    {
        if (!is_resource($stream)) {
            throw new \TypeError('Stream is expected to be a resource');
        }

        $callback = function () use (&$contents) {
            $this->rewind();
            $contents->set($this->getContents());
            return $contents->get();
        };

        $this->contents = new class($callback) {
            /** @var \Closure */
            protected $callback;
            /** @param \Closure $contents */
            public function __construct($callback) {
                $this->callback = $callback;
            }
            public function get() {
                return call_user_func($this->callback);
            }
        };
        $this->original = $contents;
        $this->stream = $stream;
    }
}
