<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class CurlRequestTest extends TestCase
{
    public function testVersion()
    {
        $version = CurlRequest::version();

        $this->assertArrayHasKey('version', $version);
    }
}
