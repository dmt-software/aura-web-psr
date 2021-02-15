<?php

namespace DMT\Test\Aura\Psr\Message;

use DMT\Aura\Psr\Factory\UriFactory;
use DMT\Aura\Psr\Message\Uri;
use Http\Psr7Test\UriIntegrationTest;
use Psr\Http\Message\UriInterface;

/**
 * Class UriTest
 *
 * @package DMT\Test\Aura\Psr\Message
 */
class UriTest extends UriIntegrationTest
{
    /**
     * Create uri.
     *
     * @param string $uri
     *
     * @return Uri|UriInterface
     */
    public function createUri($uri): Uri
    {
        return (new UriFactory())->createUri($uri);
    }

    /**
     * Overridden, percent-encoding should be case insensitively tested, the original test is not.
     *
     * @dataProvider getPaths
     * @param UriInterface $uri
     * @param string $expected
     */
    public function testPath(UriInterface $uri, $expected)
    {
        $this->assertEqualsIgnoringCase($expected, $uri->getPath());
    }
}
