<?php

namespace DMT\Test\Aura\Psr\Message;

use Aura\Web\WebFactory;
use DMT\Aura\Psr\Message\Stream;
use Http\Psr7Test\StreamIntegrationTest;
use Psr\Http\Message\StreamInterface;

class StreamTest extends StreamIntegrationTest
{
    public function testContent()
    {
        $contents = (new WebFactory([]))->newRequestContent();
        $stream = fopen('data://text/plain,' . $contents->getRaw(), 'r+');
        $request = new Stream($contents, $stream);
        $request->write('blablabla');

        $this->assertSame($request->getInnerObject(), $contents);
    }

    public function createStream($data): StreamInterface
    {
        $contents = (new WebFactory([]))->newRequestContent();

        return new Stream($contents, $data);
    }
}
