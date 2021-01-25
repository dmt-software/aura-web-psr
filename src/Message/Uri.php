<?php

namespace DMT\Aura\Psr\Message;

use Aura\Web\Exception\InvalidComponent;
use Aura\Web\Request\Url as AuraUrl;
use DMT\Aura\Psr\Helpers\HelperFactory;
use DMT\Aura\Psr\Helpers\RequestUrlHelper;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 *
 * @package DMT\Aura\Psr\Message
 */
class Uri implements UriInterface
{
    /**
     * Default ports for schemes.
     */
    public const DEFAULT_PORTS = [
        'http' => 80,
        'https' => 443,
    ];

    /** @var AuraUrl $requestUrl */
    protected $requestUrl;
    /** @var RequestUrlHelper */
    private $urlWrapper;

    /**
     * Uri constructor.
     *
     * @param AuraUrl $requestUrl
     * @param string|null $uri
     */
    public function __construct(AuraUrl $requestUrl, string $uri = null)
    {
        $this->requestUrl = $requestUrl;
        $this->urlWrapper = HelperFactory::getRequestUrlHelper($requestUrl);

        if ($uri) {
            $this->urlWrapper->set(parse_url($uri));
        }
    }

    /**
     * @return AuraUrl
     */
    public function getInnerObject(): AuraUrl
    {
        return $this->requestUrl;
    }


    /**
     * Retrieve the scheme component of the URI.
     *
     * @return string
     */
    public function getScheme(): string
    {
        return rtrim($this->urlGet(PHP_URL_SCHEME), ' :/');
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * @return string
     */
    public function getAuthority(): string
    {
        $authority = '';

        $host = $this->getHost();
        if ($host !== '') {
            $userInfo = $this->getUserInfo();
            if ($userInfo !== '') {
                $authority .= $userInfo . '@';
            }

            $authority .= $host;

            $port = $this->getPort();
            if ($port !== null) {
                $authority .= ':' . $port;
            }
        }

        return $authority;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * @return string
     */
    public function getUserInfo(): string
    {
        $user = $this->urlGet(PHP_URL_USER);
        $pass = $this->urlGet(PHP_URL_PASS);

        $userInfo = '';
        if ($user !== '') {
            $userInfo .= $user;
            if ($pass !== '') {
                $userInfo .= ':' . $pass;
            }
        }


        return $userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * @return string
     */
    public function getHost(): string
    {
        return strtolower($this->urlGet(PHP_URL_HOST));
    }

    /**
     * Retrieve the port component of the URI.
     *
     * @return null|int
     */
    public function getPort(): ?int
    {
        $port = $this->urlGet(PHP_URL_PORT, null);
        if ($port == (self::DEFAULT_PORTS[$this->getScheme()] ?? null)) {
            $port = null;
        }

        return $port > 0 ? (int)$port : null;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * @return string
     */
    public function getPath(): string
    {
        $path = $this->urlGet(PHP_URL_PATH);
        if (empty($path)) {
            return '';
        }

        $paths = array_map([$this, 'decode'], explode('/', $path));

        return implode('/', array_map('rawurlencode', $paths));
    }

    /**
     * Retrieve the query string of the URI.
     *
     * @return string
     */
    public function getQuery(): string
    {
        $query = $this->urlGet(PHP_URL_QUERY);
        if ($query === '') {
            return '';
        }

        parse_str($query, $params);

        $params = array_map([$this, 'decode'], $params);

        return preg_replace('~\=(\&|$)~', "$1", http_build_query($params, '', '&', PHP_QUERY_RFC3986));
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * @return string
     */
    public function getFragment(): string
    {
        return rawurlencode($this->decode($this->urlGet(PHP_URL_FRAGMENT)));
    }

    /**
     * Return an instance with the specified scheme.
     *
     * @param string $scheme
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withScheme($scheme): self
    {
        if (!is_string($scheme) || !preg_match('~[a-z]~i', $scheme)) {
            throw new \InvalidArgumentException('invalid scheme given');
        }

        return $this->urlSet(PHP_URL_SCHEME, strtolower($scheme) . '://');
    }

    /**
     * Return an instance with the specified user information.
     *
     * @param string $user
     * @param null|string $password
     * @return static
     */
    public function withUserInfo($user, $password = null): self
    {
        if (!is_string($user) || $user === '') {
            throw new \InvalidArgumentException('invalid user given');
        }

        if (!is_string($password) && !is_null($password)) {
            throw new \InvalidArgumentException('invalid password given');
        }

        return $this
            ->urlSet(PHP_URL_USER, $user)
            ->urlSet(PHP_URL_PASS, $password);
    }

    /**
     * Return an instance with the specified host.
     *
     * @param string $host
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withHost($host): self
    {
        if (!is_string($host)) {
            throw new \InvalidArgumentException('invalid host given');
        }

        return $this->urlSet(PHP_URL_HOST, $host);
    }

    /**
     * Return an instance with the specified port.
     *
     * @param null|int $port
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withPort($port): self
    {
        if (is_null($port)) {
            return $this->urlSet('port', '');
        }

        if (!is_int($port) || $port < 1) {
            throw new \InvalidArgumentException('invalid post given');
        }

        return $this->urlSet(PHP_URL_PORT, $port);
    }

    /**
     * Return an instance with the specified path.
     *
     * @param string $path
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withPath($path): self
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('invalid path given');
        }

        return $this->urlSet(PHP_URL_PATH, $path);
    }

    /**
     * Return an instance with the specified query string.
     *
     * @param string $query
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withQuery($query): self
    {
        if (!is_string($query)) {
            throw new \InvalidArgumentException('invalid path given');
        }

        return $this->urlSet(PHP_URL_QUERY, $query);
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * @param string $fragment
     * @return static
     */
    public function withFragment($fragment): self
    {
        if (!is_string($fragment)) {
            throw new \InvalidArgumentException('invalid fragment given');
        }

        return $this->urlSet(PHP_URL_FRAGMENT, $fragment);
    }

    /**
     * Return the string representation as a URI reference.
     *
     * @return string
     */
    public function __toString(): string
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        $uri = '';
        if ($scheme !== '') {
            $uri .= $scheme . ':';
        }

        if ($authority !== '') {
            $uri .= '//' . $authority;
        }

        $uri .= '/' . ltrim($path, ' /');

        if ($query !== '') {
            $uri .= '?' . $query;
        }

        if ($fragment !== '') {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * Decode a value.
     *
     * @param array|string $data
     * @return array|string
     */
    protected function decode($data)
    {
        if (is_array($data)) {
            return array_map([$this, __FUNCTION__], $data);
        }

        return rawurldecode((string)$data);
    }

    /**
     * Get a url part.
     *
     * @param int $component
     * @param string|null $default
     * @return string|null
     */
    private function urlGet(int $component, ?string $default = ''): ?string
    {
        try {
            return $this->requestUrl->get($component) ?? $default;
        } catch (InvalidComponent $exception) {
            return $default;
        }
    }

    /**
     * Store a url part.
     *
     * @param string|int $component
     * @param string|null $value
     * @return static
     */
    private function urlSet($component, ?string $value): self
    {
        $newInstance = clone($this);
        $newInstance->urlWrapper->set($this->requestUrl->get());
        $newInstance->urlWrapper->set($component, $value);

        return $newInstance;
    }
}
