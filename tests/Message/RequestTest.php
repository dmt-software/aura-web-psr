<?php

namespace DMT\Test\Aura\Psr\Message;

use Aura\Web\WebFactory;
use DMT\Aura\Psr\Factory\RequestFactory;
use DMT\Aura\Psr\Message\Stream;
use Http\Psr7Test\RequestIntegrationTest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class RequestTest extends RequestIntegrationTest
{
    /**
     * Create request.
     *
     * @return RequestInterface
     */
    public function createSubject(): RequestInterface
    {
        return (new RequestFactory())->createRequest('GET', '/');
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
