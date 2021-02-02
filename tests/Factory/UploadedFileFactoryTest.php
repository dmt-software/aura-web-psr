<?php

namespace DMT\Test\Aura\Psr\Factory;

use DMT\Aura\Psr\Factory\UploadedFileFactory;
use DMT\Aura\Psr\Message\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileFactoryTest extends TestCase
{
    public function testCreateUploadedFile()
    {
        $stream = new Stream(fopen('php://memory', 'r'));
        $uploadedFile = (new UploadedFileFactory())->createUploadedFile($stream);

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
        $this->assertSame($stream, $uploadedFile->getStream());
    }

    public function testCreateUnreadableUploadedFile()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new UploadedFileFactory())
            ->createUploadedFile(
                new Stream(fopen('php://output', 'w'))
            );
    }

    /**
     * This tests create from uploaded files populated from <input type=file name=file>
     */
    public function testCreateUploadedFilesFromFileUpload()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tmp, '0987654321');

        $files = [
            'file' => [
                'name' => 'image.png',
                'type' => 'image/png',
                'tmp_name' => $tmp,
                'error' => \UPLOAD_ERR_OK,
                'size' => 10,
            ]
        ];

        $uploadedFiles = (new UploadedFileFactory())->createUploadedFilesFromGlobalFiles($files);

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['file']);
        $this->assertInstanceOf(StreamInterface::class, $uploadedFiles['file']->getStream());
        $this->assertSame($files['file']['name'], $uploadedFiles['file']->getClientFilename());
        $this->assertSame($files['file']['type'], $uploadedFiles['file']->getClientMediaType());
        $this->assertSame($files['file']['error'], $uploadedFiles['file']->getError());
        $this->assertSame($files['file']['size'], $uploadedFiles['file']->getSize());
    }

    /**
     * This tests create from uploaded files populated from <input type=file name=file multiple>
     */
    public function testCreateUploadedFilesFromFileUploadMultiple()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tmp, '0987654321');

        $files = [
            'image' => [
                'name' => ['image.jpg', 'image.png'],
                'type' => ['image/jpeg', 'image/png'],
                'tmp_name' => [$tmp, $tmp],
                'error' => [0, 0],
                'size' => [10, 10],
            ]
        ];

        $uploadedFiles = (new UploadedFileFactory())->createUploadedFilesFromGlobalFiles($files);

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['image'][0]);
        $this->assertInstanceOf(StreamInterface::class, $uploadedFiles['image'][0]->getStream());
        $this->assertSame($files['image']['name'][0], $uploadedFiles['image'][0]->getClientFilename());
        $this->assertSame($files['image']['type'][0], $uploadedFiles['image'][0]->getClientMediaType());
        $this->assertSame($files['image']['error'][0], $uploadedFiles['image'][0]->getError());
        $this->assertSame($files['image']['size'][0], $uploadedFiles['image'][0]->getSize());

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['image'][1]);
        $this->assertInstanceOf(StreamInterface::class, $uploadedFiles['image'][1]->getStream());
        $this->assertSame($files['image']['name'][1], $uploadedFiles['image'][1]->getClientFilename());
        $this->assertSame($files['image']['type'][1], $uploadedFiles['image'][1]->getClientMediaType());
        $this->assertSame($files['image']['error'][1], $uploadedFiles['image'][1]->getError());
        $this->assertSame($files['image']['size'][1], $uploadedFiles['image'][1]->getSize());
    }

    /**
     * This tests create from uploaded files populated from <input type=file name=file>
     */
    public function testCreateUploadedFilesFromNestedFileUpload()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tmp, '0987654321');

        $files = [
            'import' => [
                'name' => ['file' => 'import.csv'],
                'type' => ['file' => 'plain/text'],
                'tmp_name' => ['file' => $tmp],
                'error' => ['file' => \UPLOAD_ERR_OK],
                'size' => ['file' => 0],
            ]
        ];

        $uploadedFiles = (new UploadedFileFactory())->createUploadedFilesFromGlobalFiles($files);

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['import']['file']);
        $this->assertInstanceOf(StreamInterface::class, $uploadedFiles['import']['file']->getStream());
        $this->assertSame($files['import']['name']['file'], $uploadedFiles['import']['file']->getClientFilename());
        $this->assertSame($files['import']['type']['file'], $uploadedFiles['import']['file']->getClientMediaType());
        $this->assertSame($files['import']['error']['file'], $uploadedFiles['import']['file']->getError());
        $this->assertSame($files['import']['size']['file'], $uploadedFiles['import']['file']->getSize());
    }

    public function testCreateUploadedFilesFromEmptyFile()
    {
        $files = [
            'file' => [
                'name' => '',
                'type' => '',
                'tmp_name' => '',
                'error' => \UPLOAD_ERR_NO_FILE,
                'size' => null,
            ]
        ];

        $uploadedFiles = (new UploadedFileFactory())->createUploadedFilesFromGlobalFiles($files);

        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['file']);

        $this->assertSame($files['file']['error'], $uploadedFiles['file']->getError());
        $this->assertEmpty($uploadedFiles['file']->getClientFilename());
        $this->assertEmpty($uploadedFiles['file']->getClientMediaType());
        $this->assertEmpty($uploadedFiles['file']->getSize());

        $this->expectException(\RuntimeException::class);

        $uploadedFiles['file']->getStream(); // do not stream from erroneous files.
    }
}
