<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\Request\Headers;
use DMT\Aura\Psr\Helpers\RequestHeadersHelper;
use PHPUnit\Framework\TestCase;

class RequestHeadersHelperTest extends TestCase
{
    public function testSetHeaders()
    {
        $data = ['keY' => 'value'];
        $headers = new Headers(['HTTP_HOST' => 'example.com']);
        $this->assertSame('example.com', $headers->get('host'));

        $helper = new RequestHeadersHelper($headers);
        $helper->setObjectProperty('data', $data);

        $this->assertSame($data, $headers->get());
        /*
         * Header storage is done with preserved case, but retrieved as lower case.
         * Access with headers::get is therefore restricted to lowercase headers.
         */
        $this->assertNull($headers->get('keY'));
    }
}
