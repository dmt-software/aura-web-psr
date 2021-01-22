<?php

namespace DMT\Test\Aura\Psr\Message;

use Aura\Web\Request\Url;
use DMT\Aura\Psr\Message\Uri;
use Http\Psr7Test\UriIntegrationTest;
use Psr\Http\Message\UriInterface;

class UriTest extends UriIntegrationTest
{
    /**
     * Create uri.
     *
     * @param string $uri
     *
     * @return UriInterface
     */
    public function createUri($uri): UriInterface
    {
        return new Uri(new Url([]), $uri);
    }

    /**
     * Overridden, percent-encoding is case insensitive, the original test is not.
     *
     * @dataProvider getPaths
     */
    public function testPath(UriInterface $uri, $expected)
    {
        $this->assertEqualsIgnoringCase($expected, $uri->getPath());
    }
}
