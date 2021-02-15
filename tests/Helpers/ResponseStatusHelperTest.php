<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\Response\Status;
use DMT\Aura\Psr\Helpers\ResponseStatusHelper;
use PHPUnit\Framework\TestCase;

class ResponseStatusHelperTest extends TestCase
{
    public function testSetProtocolVersion()
    {
        $status = new Status();
        $helper = new ResponseStatusHelper($status);
        $helper->setObjectProperty('version', '2');

        $this->assertSame('2', $status->getVersion());
    }
}
