<?php

namespace DMT\Test\Aura\Psr\Message;

use Aura\Web\Request\Values;
use DMT\Aura\Psr\Factory\ServerRequestFactory;
use DMT\Aura\Psr\Message\UploadedFile;
use Http\Psr7Test\ServerRequestIntegrationTest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class ServerRequestTest extends ServerRequestIntegrationTest
{
    /**
     * Create request.
     *
     * @return ServerRequestInterface
     */
    public function createSubject(): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest('GET', '/', $_SERVER);
    }

    /**
     * @param string $data
     * @return UploadedFile|UploadedFileInterface
     */
    protected function buildUploadableFile($data): UploadedFile
    {
        $file = tempnam(sys_get_temp_dir(), 'file');
        file_put_contents($file, $data);

        $file = [
            'tmp_name' => $file,
            'name' => 'newfilename',
            'error' => UPLOAD_ERR_OK,
            'size' => strlen($data),
            'type' => 'text/plain',
        ];

        return new UploadedFile(new Values($file));
    }
}
