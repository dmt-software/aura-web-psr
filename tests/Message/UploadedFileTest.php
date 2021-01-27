<?php

namespace DMT\Test\Aura\Psr\Message;

use DMT\Aura\Psr\Factory\UploadedFileFactory;
use DMT\Aura\Psr\Message\Stream;
use DMT\Aura\Psr\Message\UploadedFile;
use Http\Psr7Test\UploadedFileIntegrationTest;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFileTest
 *
 * @package DMT\Test\Aura\Psr\Message
 */
class UploadedFileTest extends UploadedFileIntegrationTest
{
    /**
     * Create uploaded file.
     *
     * @return UploadedFile|UploadedFileInterface
     */
    public function createSubject(): UploadedFile
    {
        return (new UploadedFileFactory())->createUploadedFile(new Stream(''));
    }
}
