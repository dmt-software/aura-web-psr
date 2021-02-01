<?php

namespace DMT\Aura\Psr\Factory;

use DMT\Aura\Psr\Message\UploadedFile;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFileFactory
 *
 * @package DMT\Aura\Psr\Factory
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /** @var StreamFactoryInterface $streamFactory */
    protected $streamFactory;

    /**
     * UploadedFileFactory constructor.
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(StreamFactoryInterface $streamFactory = null)
    {
        $this->streamFactory = $streamFactory ?? new StreamFactory();
    }

    /**
     * Create a new uploaded file.
     *
     * If a size is not provided it will be determined by checking the size of
     * the file.
     *
     * @see http://php.net/manual/features.file-upload.post-method.php
     * @see http://php.net/manual/features.file-upload.errors.php
     *
     * @param StreamInterface $stream Underlying stream representing the uploaded file content.
     * @param int $size in bytes
     * @param int $error PHP file upload error
     * @param string $clientFilename Filename as provided by the client, if any.
     * @param string $clientMediaType Media type as provided by the client, if any.
     *
     * @return UploadedFileInterface
     *
     * @throws \InvalidArgumentException If the file resource is not readable.
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    /**
     * Create uploaded files from $_FILES.
     *
     * @param array $uploadedFiles
     * @return array|UploadedFileInterface[]
     */
    public function createUploadedFilesFromGlobalFiles(array $uploadedFiles): array
    {
        foreach ($uploadedFiles as $file => &$uploadedFile) {
            if (!is_array($uploadedFile) || !array_key_exists('tmp_name', $uploadedFile)) {
                continue;
            }

            if (is_array($uploadedFile['tmp_name'])) {
                $files = [];
                foreach ($uploadedFile as $key => $values) {
                    if (is_string(key($values))) {
                        $files[key($values)][$key] = current($values);
                    }
                }
                if ($files) {
                    $uploadedFile = $this->createUploadedFilesFromGlobalFiles($files);
                }
            }

            if ($this->isUploadedFileEntry($uploadedFile)) {
                $uploadedFile = $this->asUploadedFileInstance($uploadedFile);
            }
        }

        return $uploadedFiles;
    }

    /**
     * @param array $uploadedFileEntry
     * @return bool
     */
    private function isUploadedFileEntry($uploadedFileEntry): bool
    {
        if (!is_array($uploadedFileEntry) || !array_key_exists('tmp_name', $uploadedFileEntry)) {
            return false;
        }

        return is_string($uploadedFileEntry['tmp_name']) || is_string(current((array)$uploadedFileEntry['tmp_name']));
    }

    /**
     * @param array $uploadedFile
     * @return array|UploadedFileInterface
     */
    private function asUploadedFileInstance($uploadedFile)
    {
        if (is_array($uploadedFile['tmp_name'])) {
            return array_map([$this, __FUNCTION__], $this->normalizeUploadedFileArray($uploadedFile));
        }

        if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
            $stream = $this->streamFactory->createStreamFromFile($uploadedFile['tmp_name']);
        } else {
            $stream = $this->streamFactory->createStream('');
        }

        return $this->createUploadedFile(
            $stream,
            $uploadedFile['size'] ?? null,
            $uploadedFile['error'] ?? UPLOAD_ERR_OK,
            $uploadedFile['name'] ?? null,
            $uploadedFile['type'] ?? null
        );
    }

    /**
     * @param array $uploadedFile
     * @return array
     */
    private function normalizeUploadedFileArray(array $uploadedFile)
    {
        if (!is_string($uploadedFile['tmp_name'])) {
            $files = [];
            for ($i = 0; $i < count($uploadedFile['tmp_name']); $i++) {
                $files[$i] = [];
                foreach ($uploadedFile as $key => $value) {
                    $files[$i][$key] = $value[$i] ?? null;
                }
            }
            $uploadedFile = $files;
        }

        return $uploadedFile;
    }
}
