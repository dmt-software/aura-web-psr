<?php

namespace DMT\Test\Aura\Psr\Message;

use DMT\Aura\Psr\Factory\RequestFactory;
use DMT\Aura\Psr\Message\Request;
use Http\Psr7Test\RequestIntegrationTest;
use Psr\Http\Message\RequestInterface;

/**
 * Class RequestTest
 *
 * @package DMT\Test\Aura\Psr\Message
 */
class RequestTest extends RequestIntegrationTest
{
    /**
     * Create request.
     *
     * @return Request|RequestInterface
     */
    public function createSubject(): Request
    {
        return (new RequestFactory())->createRequest('GET', '/');
    }
}
