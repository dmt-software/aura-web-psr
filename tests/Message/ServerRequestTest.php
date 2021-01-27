<?php

namespace DMT\Test\Aura\Psr\Message;

use DMT\Aura\Psr\Factory\ServerRequestFactory;
use DMT\Aura\Psr\Message\ServerRequest;
use Http\Psr7Test\ServerRequestIntegrationTest;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ServerRequestTest
 *
 * @package DMT\Test\Aura\Psr\Message
 */
class ServerRequestTest extends ServerRequestIntegrationTest
{
    /**
     * Create request.
     *
     * @return ServerRequest|ServerRequestInterface
     */
    public function createSubject(): ServerRequest
    {
        return (new ServerRequestFactory())->createServerRequest('GET', '/', $_SERVER);
    }
}
