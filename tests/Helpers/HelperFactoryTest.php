<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\WebFactory;
use DMT\Aura\Psr\Helpers;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HelperFactoryTest extends TestCase
{
    /**
     * @dataProvider provideObject
     *
     * @param object $object
     * @param string $helper
     */
    public function testCreateHelper($object, $helper)
    {
        $instance = (new Helpers\HelperFactory())->createHelper($object);

        $this->assertInstanceOf($helper, $instance);
        $this->assertInstanceOf(get_class($object), $instance);
    }

    public function provideObject()
    {
        $factory = new WebFactory([]);

        return [
            [$factory->newRequest(), Helpers\RequestHelper::class],
            [$factory->newRequestContent(), Helpers\RequestContentHelper::class],
            [$factory->newRequestHeaders(), Helpers\RequestHeadersHelper::class],
            [$factory->newRequestMethod(), Helpers\RequestMethodHelper::class],
            [$factory->newRequestUrl(), Helpers\RequestUrlHelper::class],
            [$factory->newResponse(), Helpers\ResponseHelper::class],
            [$factory->newResponseHeaders(), Helpers\ResponseHeadersHelper::class],
            [$factory->newResponseStatus(), Helpers\ResponseStatusHelper::class],
        ];
    }

    public function testCreateHelperFailure()
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = new WebFactory([]);
        $helperFactory = new Helpers\HelperFactory();

        $helperFactory->createHelper($factory->newRequestServer());
    }
}
