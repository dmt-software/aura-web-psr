<?php

namespace DMT\Test\Aura\Psr\Factory;

use DMT\Aura\Psr\Factory\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactoryTest extends TestCase
{
    public function testCreateServerRequest()
    {
        $serverRequest = (new ServerRequestFactory())->createServerRequest('GET', '/', $_SERVER);

        $this->assertInstanceOf(ServerRequestInterface::class, $serverRequest);
        $this->assertEquals($_SERVER, $serverRequest->getServerParams());
    }
}
