<?php

namespace DMT\Test\Aura\Psr\Factory;

use DMT\Aura\Psr\Factory\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class StreamFactoryTest extends TestCase
{
    public function testCreateStreamFromFile()
    {
        $expected = 'Etiam gravida tortor';

        $file = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($file, $expected);

        $stream = (new StreamFactory())->createStreamFromFile($file);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame($expected, $stream->getContents());
    }

    public function testCreateStreamFromFileIncorrectMode()
    {
        $this->expectException(\InvalidArgumentException::class);

        $file = tempnam(sys_get_temp_dir(), 'php');

        (new StreamFactory())->createStreamFromFile($file, 'p');
    }

    public function testCreateStreamFromUnreadableFile()
    {
        $this->expectException(\RuntimeException::class);

        (new StreamFactory())->createStreamFromFile('missing-file', 'r');
    }

    public function testCreateStreamFromResource()
    {
        $expected = 'Proin non commodo dolor';

        $resource = fopen('php://memory', 'w+');
        fwrite($resource, $expected);
        rewind($resource);

        $stream = (new StreamFactory())->createStreamFromResource($resource);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame($expected, $stream->getContents());
    }

    public function testCreateStream()
    {
        $expected = 'Lorem ipsum';

        $stream = (new StreamFactory())->createStream($expected);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame($expected, $stream->getContents());
    }
}
