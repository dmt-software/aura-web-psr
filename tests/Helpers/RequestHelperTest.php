<?php

namespace DMT\Test\Aura\Psr\Helpers;

use Aura\Web\Request;
use Aura\Web\WebFactory;
use DMT\Aura\Psr\Helpers\RequestHelper;
use PHPUnit\Framework\TestCase;

class RequestHelperTest extends TestCase
{
    public function testCloneObject()
    {
        $request = (new WebFactory([]))->newRequest();
        $helper = new RequestHelper($request);

        /** @var Request $newRequest */
        $newRequest = $helper->cloneObject();

        $this->assertInstanceOf(Request::class, $newRequest);
        $this->assertNotSame($request, $newRequest);
        $this->assertEquals($request, $newRequest);

        $this->assertInstanceOf(Request\Client::class, $newRequest->client);
        $this->assertNotSame($request->client, $newRequest->client);
        $this->assertEquals($request->client, $newRequest->client);

        $this->assertInstanceOf(Request\Content::class, $newRequest->content);
        $this->assertNotSame($request->content, $newRequest->content);
        $this->assertEquals($request->content, $newRequest->content);

        $this->assertInstanceOf(Request\Values::class, $newRequest->cookies);
        $this->assertNotSame($request->cookies, $newRequest->cookies);
        $this->assertEquals($request->cookies, $newRequest->cookies);

        $this->assertInstanceOf(Request\Values::class, $newRequest->env);
        $this->assertNotSame($request->env, $newRequest->env);
        $this->assertEquals($request->env, $newRequest->env);

        $this->assertInstanceOf(Request\Files::class, $newRequest->files);
        $this->assertNotSame($request->files, $newRequest->files);
        $this->assertEquals($request->files, $newRequest->files);

        $this->assertInstanceOf(Request\Headers::class, $newRequest->headers);
        $this->assertNotSame($request->headers, $newRequest->headers);
        $this->assertEquals($request->headers, $newRequest->headers);

        $this->assertInstanceOf(Request\Method::class, $newRequest->method);
        $this->assertNotSame($request->method, $newRequest->method);
        $this->assertEquals($request->method, $newRequest->method);

        $this->assertInstanceOf(Request\Params::class, $newRequest->params);
        $this->assertNotSame($request->params, $newRequest->params);
        $this->assertEquals($request->params, $newRequest->params);

        $this->assertInstanceOf(Request\Values::class, $newRequest->post);
        $this->assertNotSame($request->post, $newRequest->post);
        $this->assertEquals($request->post, $newRequest->post);

        $this->assertInstanceOf(Request\Values::class, $newRequest->query);
        $this->assertNotSame($request->query, $newRequest->query);
        $this->assertEquals($request->query, $newRequest->query);

        $this->assertInstanceOf(Request\Values::class, $newRequest->server);
        $this->assertNotSame($request->server, $newRequest->server);
        $this->assertEquals($request->server, $newRequest->server);

        $this->assertInstanceOf(Request\Url::class, $newRequest->url);
        $this->assertNotSame($request->url, $newRequest->url);
        $this->assertEquals($request->url, $newRequest->url);
    }
}
