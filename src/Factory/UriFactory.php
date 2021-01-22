<?php

namespace DMT\Aura\Psr\Factory;

use Aura\Web\WebFactory;
use DMT\Aura\Psr\Message\Uri;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class UriFactory
 *
 * @package DMT\Aura\Psr\Factory
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * Create a new URI.
     *
     * @param string $uri
     * @return UriInterface
     * @throws \InvalidArgumentException
     */
    public function createUri(string $uri = ''): UriInterface
    {
        if ($uri !== '' && parse_url($uri) === false) {
            throw new \InvalidArgumentException('illegal uri given');
        }

        return new Uri((new WebFactory([]))->newRequestUrl(), $uri);
    }
}
