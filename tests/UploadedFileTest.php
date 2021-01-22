<?php

namespace DMT\Test\Aura\Psr;

use Aura\Web\Request\Values;
use DMT\Aura\Psr\UploadedFile;
use Http\Psr7Test\UploadedFileIntegrationTest;

class UploadedFileTest extends UploadedFileIntegrationTest
{
    public function createSubject()
    {
        $file = tempnam(sys_get_temp_dir(), 'file');
        file_put_contents($file, 'foo');

        $file = [
            'tmp_name' => $file,
            'name' => 'newfilename',
            'error' => UPLOAD_ERR_OK,
            'size' => 3,
            'type' => 'text/plain',
        ];

        return new UploadedFile(new Values($file));
    }
}
