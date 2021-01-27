<?php

namespace DMT\Test\Aura\Psr\Message;

use DMT\Aura\Psr\Factory\ResponseFactory;
use DMT\Aura\Psr\Message\Response;
use Http\Psr7Test\ResponseIntegrationTest;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseTest
 *
 * @package DMT\Test\Aura\Psr\Message
 */
class ResponseTest extends ResponseIntegrationTest
{
    /**
     * Create response.
     *
     * @return Response|ResponseInterface
     */
    public function createSubject(): Response
    {
        return (new ResponseFactory())->createResponse();
    }

    /**
     * Extra test if wrapped aura response accepts http version 2.0.
     * The original aura response does not.
     */
    public function testWithUpdatedProtocolVersion()
    {
        $response = $this->createSubject();
        $newResponse = $response->withProtocolVersion('2.0');

        $this->assertSame('2.0', $newResponse->getProtocolVersion());
    }
}
