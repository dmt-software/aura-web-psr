<?php

namespace DMT\Test\Aura\Psr;

use Aura\Web\WebFactory;
use DMT\Aura\Psr\Response;
use DMT\Aura\Psr\Stream;
use Http\Psr7Test\ResponseIntegrationTest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends ResponseIntegrationTest
{
    protected $skippedTests = [
        'testGetHeaders' => 'Overridden',
        'testWithHeader' => 'Overridden',
    ];

    /**
     * Overridden because Aura Response does not preserve the casing for the headers.
     */
    public function testGetHeaders()
    {
        $response = $this->createSubject();
        $headerObject = $response->getInnerObject()->headers;

        $headerObject->set('content-type', 'text/html');
        $headerObject->set('Accept-Encoding', 'deflate');
        $headerObject->add('accept-encoding', 'gzip');

        $headers = $response->getHeaders();

        $this->assertContains('text/html', $headers['Content-Type']);
        $this->assertContains('deflate', $headers['Accept-Encoding']);
        $this->assertContains('gzip', $headers['Accept-Encoding']);
    }

    /**
     * Overridden Aura Response treats empty header values as remove header.
     */
    public function testWithHeader()
    {
        $initialMessage = $this->getMessage();
        $original = clone $initialMessage;

        $message = $initialMessage->withHeader('content-type', 'text/html');
        $this->assertNotSameObject($initialMessage, $message);
        $this->assertEquals($initialMessage, $original, 'Message object MUST not be mutated');
        $this->assertEquals('text/html', $message->getHeaderLine('content-type'));

        $message = $initialMessage->withHeader('content-type', 'text/plain');
        $this->assertEquals('text/plain', $message->getHeaderLine('content-type'));

        $message = $initialMessage->withHeader('Content-TYPE', 'text/script');
        $this->assertEquals('text/script', $message->getHeaderLine('content-type'));

        $message = $initialMessage->withHeader('x-foo', ['bar', 'baz']);
        $this->assertRegExp('|bar, ?baz|', $message->getHeaderLine('x-foo'));
    }

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
