<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class MKJHeaderParserTest extends TestCase {

    function testParseHeadersBlankPage() {
        $parser = new MKJHeaderParser();
        
        $filename = __DIR__ . '/../../resources/intentionallyblankpage.com';
        $contents = file_get_contents($filename);
        
        $headers = $parser->parseHeaders($contents);
        $this->assertArrayHasKey($key = 0, $headers);
    }

    function testParseHeadersRedirectToBlankPage() {
        $parser = new MKJHeaderParser();
        
        $filename = __DIR__ . '/../../resources/redirect-to-intentionallyblankpage.com';
        $contents = file_get_contents($filename);
        var_dump($contents);
        $headers = $parser->parseHeaders($contents);
        var_dump($headers);
        $count = count($headers);
        $this->assertEquals($expected = 2, $count);
    }

}