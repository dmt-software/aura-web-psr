<?php

namespace DMT\Test\Aura\Psr\Message;

use Aura\Web\WebFactory;
use DMT\Aura\Psr\Message\Stream;
use Http\Psr7Test\StreamIntegrationTest;
use Psr\Http\Message\StreamInterface;

class StreamTest extends StreamIntegrationTest
{
    public function createStream($data): StreamInterface
    {
        return new Stream($data);
    }
}
