<?php

namespace DMT\Test\Aura\Psr\Factory;

use DMT\Aura\Psr\Factory\UriFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriFactoryTest extends TestCase
{
    /**
     * @dataProvider provideUri
     *
     * @param string $uri
     * @param string $expected
     */
    public function testCreateUri(string $uri, string $expected)
    {
        $uriObject = (new UriFactory())->createUri($uri);

        $this->assertInstanceOf(UriInterface::class, $uriObject);
        $this->assertSame($expected, (string)$uriObject);
    }

    public function provideUri()
    {
        return [
            ['path', '/path'],
            ['ftp://domain/', 'ftp://domain/'],
            ['https://user@example.org', 'https://user@example.org/'],
            ['http://localhost/?d=1', 'http://localhost/?d=1'],
            ['http://localhost#caption', 'http://localhost/#caption'],
            ['https://localhost/show?r=m%26m', 'https://localhost/show?r=m%26m'],
        ];
    }

    /**
     * @dataProvider provideInvalidUri
     *
     * @param string $uri
     */
    public function testCreateInvalidUri(string $uri)
    {
        $this->expectException(InvalidArgumentException::class);

        (new UriFactory())->createUri($uri);
    }

    public function provideInvalidUri()
    {
        return [
            ['https:///path'],
            ['file://@/path'],
        ];
    }
}
