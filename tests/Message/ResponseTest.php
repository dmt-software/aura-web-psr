<?php

namespace DMT\Test\Aura\Psr\Message;

use Aura\Web\WebFactory;
use DMT\Aura\Psr\Message\Response;
use DMT\Aura\Psr\Message\Stream;
use Http\Psr7Test\ResponseIntegrationTest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends ResponseIntegrationTest
{

    public function testWithUpdatedProtocolVersion()
    {
        $response = $this->createSubject();
        $newResponse = $response->withProtocolVersion('2.0');

        $this->assertSame('2.0', $newResponse->getProtocolVersion());
    }

    /**
     * Create response.
     *
     * @return Response|ResponseInterface
     */
    public function createSubject(): Response
    {
        $response = (new WebFactory([]))->newResponse();

        return new Response($response);
    }

    /**
     * Build stream.
     *
     * @param string $data
     * @return Stream|StreamInterface
     */
    protected function buildStream($data): Stream
    {
        $contents = $this->createSubject()->getInnerObject()->content;

        return new Stream($contents, fopen('data://text/plain,' . $data, 'r+'));
    }
}
