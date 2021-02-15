<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\Request\Url;
use DMT\Aura\Psr\Helpers\RequestUrlHelper;
use PHPUnit\Framework\TestCase;

class RequestUrlHelperTest extends TestCase
{
    public function testUrlSet()
    {
        $url = new Url([]);
        $helper = new RequestUrlHelper($url);
        $helper->setObjectProperty('string', $uri = 'http://user:pass@example.com/home');

        $this->assertSame($uri, $url->get());
    }

    public function testParseUrlSet()
    {
        $url = new Url([]);
        $helper = new RequestUrlHelper($url);
        $helper->setObjectProperty('parts', parse_url($uri = 'https://user:pass@example.com/home?a=true#b'));

        $this->assertSame(parse_url($uri, PHP_URL_SCHEME), $url->get(PHP_URL_SCHEME));
        $this->assertSame(parse_url($uri, PHP_URL_HOST), $url->get(PHP_URL_HOST));
        $this->assertSame(parse_url($uri, PHP_URL_PORT), $url->get(PHP_URL_PORT));
        $this->assertSame(parse_url($uri, PHP_URL_USER), $url->get(PHP_URL_USER));
        $this->assertSame(parse_url($uri, PHP_URL_PASS), $url->get(PHP_URL_PASS));
        $this->assertSame(parse_url($uri, PHP_URL_PATH), $url->get(PHP_URL_PATH));
        $this->assertSame(parse_url($uri, PHP_URL_QUERY), $url->get(PHP_URL_QUERY));
        $this->assertSame(parse_url($uri, PHP_URL_FRAGMENT), $url->get(PHP_URL_FRAGMENT));
    }

    public function testUrlGet()
    {
        $url = new Url([]);
        $helper = new RequestUrlHelper($url);
        $helper->setObjectProperty('string', null);
        $helper->setObjectProperty('parts', $parts = parse_url('https://user:pass@example.com/home?a=true#b'));

        $this->assertSame($parts, $helper->getObjectProperty('parts'));
        $this->assertSame('default', $helper->getObjectProperty('string', 'default'));
    }
}
