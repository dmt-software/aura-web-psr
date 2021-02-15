<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\Response\Headers;
use DMT\Aura\Psr\Helpers\ResponseHeadersHelper;
use PHPUnit\Framework\TestCase;

class ResponseHeadersHelperTest extends TestCase
{
    public function testHeaders()
    {
        $values = [
            'keY' => 'value',
            'Content-Type' => 'text/html'
        ];
        $headers = new Headers();

        $helper = new ResponseHeadersHelper($headers);
        $helper->setObjectProperty('headers', $values);

        $this->assertSame($values, $headers->get());
        /*
         * Header storage is done with preserved case, but retrieval is formatted.
         * Access with headers::get is therefore restricted to headers in a "Dotted-Wordcased" format.
         */
        $this->assertNull($headers->get('keY'));
        $this->assertNull($headers->get('content_type'));
    }
}
