<?php

namespace DMT\Test\Aura\Psr\Message;

use Aura\Web\WebFactory;
use DMT\Aura\Psr\Message\Request;
use DMT\Aura\Psr\Message\Stream;
use Http\Psr7Test\RequestIntegrationTest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class RequestTest extends RequestIntegrationTest
{
    protected $skippedTests = [
        'testMethodIsCaseSensitive' => 'Aura Request stores methods uppercase',
        'testGetHeaders' => 'Overridden',
        'testWithoutHeader' => 'Overridden',
    ];

    /**
     * Overridden because Aura Request does not preserve the casing for the headers.
     */
    public function testGetHeaders()
    {
        $globals = [
            '_SERVER' => [
                'CONTENT_TYPE' => 'text/html',
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.88',
                'HTTP_ACCEPT_ENCODING' => 'gzip,deflate,br'
            ]
        ];

        $auraRequest = (new WebFactory($globals))->newRequest();
        $request = new Request($auraRequest);

        $headers = $request->getHeaders();

        $this->assertContains('text/html', $headers['CONTENT_TYPE']);
        $this->assertContains('application/xml;q=0.9', $headers['HTTP_ACCEPT']);
        $this->assertContains('deflate', $headers['HTTP_ACCEPT_ENCODING']);
        $this->assertContains('gzip', $headers['HTTP_ACCEPT_ENCODING']);
    }

    /**
     * Overridden because Aura Request does not preserve the casing for the headers.
     */
    public function testWithoutHeader()
    {
        $message = $this->createSubject();
        $original = clone($message);

        $request = $message
            ->withHeader('Foo', 'bar')
            ->withAddedHeader('Content-type', 'text/html')
            ->withAddedHeader('content-type', 'text/plain');

        $this->assertNotSameObject($request, $message);
        $this->assertEquals($original, $message);

        $request = $request->withoutHeader('foo');

        $this->assertFalse($request->hasHeader('Foo'));
    }

    /**
     * Create request.
     *
     * @return RequestInterface
     */
    public function createSubject(): RequestInterface
    {
        $request = (new WebFactory([]))->newRequest();

        return new Request($request);
    }

    /**
     * Build stream.
     *
     * @param string $data
     * @return Stream|StreamInterface
     */
    protected function buildStream($data): Stream
    {
        $contents = (new WebFactory([]))->newRequestContent();

        return new Stream($contents, fopen('data://text/plain,' . $data, 'r+'));
    }
}
