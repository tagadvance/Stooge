<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class CurlExceptionTest extends TestCase
{
    public function testGetErrorMessage()
    {
        $e = new CurlException('foo', CURLE_UNSUPPORTED_PROTOCOL);

        $this->assertSame(curl_strerror(CURLE_UNSUPPORTED_PROTOCOL), $e->getErrorMessage());
    }

    public function testCodeIsOptional()
    {
        $e = new CurlException('foo');

        $this->assertSame(0, $e->getCode());
    }
}
