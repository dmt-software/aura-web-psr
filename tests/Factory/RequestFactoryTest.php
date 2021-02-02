<?php

namespace DMT\Test\Aura\Psr\Factory;

use DMT\Aura\Psr\Factory\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RequestFactoryTest extends TestCase
{
    public function testCreateRequest()
    {
        $request = (new RequestFactory())->createRequest('POST', 'http://example.com/path');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame('POST', $request->getMethod());
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertSame('http://example.com/path', (string)$request->getUri());
    }
}
