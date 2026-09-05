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
}
