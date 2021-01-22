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
    /** @var WebFactory $webFactory */
    private $webFactory;

    /**
     * UriFactory constructor.
     *
     * @param WebFactory|null $webFactory
     */
    public function __construct(WebFactory $webFactory = null)
    {
        $this->webFactory = $webFactory ?? new WebFactory([]);
    }

    /**
     * Create a new URI.
     *
     * @param string $uri
     * @return UriInterface
     * @throws \InvalidArgumentException
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($this->webFactory->newRequestUrl(), $uri);
    }
}
