<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\Request\Method;
use DMT\Aura\Psr\Helpers\RequestMethodHelper;
use PHPUnit\Framework\TestCase;

class RequestMethodHelperTest extends TestCase
{
    public function testSetMethod()
    {
        $method = new Method([], []);
        $helper = new RequestMethodHelper($method);
        $helper->setObjectProperty('value', 'CUSTOM');

        $this->assertSame('CUSTOM', $method->get());
        $this->assertTrue($method->isCustom());
    }
}
