<?php

namespace DMT\Test\Aura\Psr\Factory;

use DMT\Aura\Psr\Factory\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseFactoryTest extends TestCase
{
    public function testCreateResponse()
    {
        $expected = 'Custom message';
        $response = (new ResponseFactory())->createResponse(200, $expected);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($expected, $response->getReasonPhrase());
    }
}
