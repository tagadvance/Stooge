<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class CurlRequestTest extends TestCase
{
    public function testCurlSessionIsACurlHandle()
    {
        $request = new CurlRequest();

        $this->assertInstanceOf(\CurlHandle::class, $request->getCurlSession());
    }

    public function testCloneCopiesTheCurlSession()
    {
        $request = new CurlRequest();
        $clone = clone $request;

        $this->assertInstanceOf(\CurlHandle::class, $clone->getCurlSession());
        $this->assertNotSame($request->getCurlSession(), $clone->getCurlSession());
    }

    public function testVersion()
    {
        $version = CurlRequest::version();

        $this->assertArrayHasKey('version', $version);
    }

    public function testAutoDetectUserAgentForwardsTheInboundUserAgent()
    {
        $server = $_SERVER;
        $_SERVER['HTTP_USER_AGENT'] = 'Test/1.0';
        $_SERVER['HTTP_REFERER'] = 'http://example.com/';
        try {
            $request = new CurlRequest();
            $request->autoDetectUserAgent();

            $this->assertSame('Test/1.0', $request->getOption('USERAGENT'));
        } finally {
            $_SERVER = $server;
        }
    }

    public function testAutoDetectUserAgentFallsBackToChrome()
    {
        $server = $_SERVER;
        unset($_SERVER['HTTP_USER_AGENT']);
        $_SERVER['HTTP_REFERER'] = 'http://example.com/';
        try {
            $request = new CurlRequest();
            $request->autoDetectUserAgent();

            $this->assertSame(USER_AGENT_CHROME, $request->getOption('USERAGENT'));
        } finally {
            $_SERVER = $server;
        }
    }
}
