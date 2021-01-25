<?php

namespace DMT\Test\Aura\Psr\Message;

use Aura\Web\Request\Values;
use DMT\Aura\Psr\Message\Stream;
use DMT\Aura\Psr\Message\UploadedFile;
use Http\Psr7Test\UploadedFileIntegrationTest;

class UploadedFileTest extends UploadedFileIntegrationTest
{
    public function createSubject()
    {
        return new UploadedFile(new Stream(''), null, UPLOAD_ERR_OK, 'newfilename', 'text/plain');
    }
}
