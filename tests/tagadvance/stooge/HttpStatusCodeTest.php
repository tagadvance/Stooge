<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class HttpStatusCodeTest extends TestCase
{
    public function testValueOf()
    {
        $value = HttpStatusCode::valueOf(HttpStatusCode::OK);
        $this->assertEquals($expected = 'OK', $value);

        $value = HttpStatusCode::valueOf(WebDAV::PROCESSING);
        $this->assertEquals($expected = 'PROCESSING', $value);
    }

    public function testValueOfUnknownCode()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('unknown status code: 299');

        HttpStatusCode::valueOf(299);
    }

}
