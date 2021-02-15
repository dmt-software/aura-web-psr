<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\Request\Content;
use DMT\Aura\Psr\Helpers\RequestContentHelper;
use DMT\Aura\Psr\Message\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class RequestContentHelperTest extends TestCase
{
    public function testStreamAsContent()
    {
        $stream = new Stream(fopen('php://memory', 'r+b'));
        $content = new Content([]);

        $helper = new RequestContentHelper($content);
        $helper->setObjectProperty('raw', $stream);

        $stream->write('example');

        $this->assertInstanceOf(StreamInterface::class, $content->getRaw());
        $this->assertSame('example', $content->get());
    }
}
