<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\Response;
use Aura\Web\WebFactory;
use DMT\Aura\Psr\Helpers\ResponseHelper;
use PHPUnit\Framework\TestCase;

class ResponseHelperTest extends TestCase
{
    public function testCloneObject()
    {
        $response = (new WebFactory([]))->newResponse();
        $helper = new ResponseHelper($response);

        /** @var Response $newResponse */
        $newResponse = $helper->cloneObject();

        $this->assertInstanceOf(Response::class, $newResponse);
        $this->assertNotSame($response, $newResponse);
        $this->assertEquals($response, $newResponse);

        $this->assertInstanceOf(Response\Cache::class, $newResponse->cache);
        $this->assertNotSame($response->cache, $newResponse->cache);
        $this->assertEquals($response->cache, $newResponse->cache);

        $this->assertInstanceOf(Response\Content::class, $newResponse->content);
        $this->assertNotSame($response->content, $newResponse->content);
        $this->assertEquals($response->content, $newResponse->content);

        $this->assertInstanceOf(Response\Cookies::class, $newResponse->cookies);
        $this->assertNotSame($response->cookies, $newResponse->cookies);
        $this->assertEquals($response->cookies, $newResponse->cookies);

        $this->assertInstanceOf(Response\Headers::class, $newResponse->headers);
        $this->assertNotSame($response->headers, $newResponse->headers);
        $this->assertEquals($response->headers, $newResponse->headers);

        $this->assertInstanceOf(Response\Redirect::class, $newResponse->redirect);
        $this->assertNotSame($response->redirect, $newResponse->redirect);
        $this->assertEquals($response->redirect, $newResponse->redirect);

        $this->assertInstanceOf(Response\Status::class, $newResponse->status);
        $this->assertNotSame($response->status, $newResponse->status);
        $this->assertEquals($response->status, $newResponse->status);
    }
}
