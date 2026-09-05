<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class MKJHeaderParserTest extends TestCase
{
    public function testParseHeadersBlankPage()
    {
        $parser = new MKJHeaderParser();

        $filename = __DIR__ . '/../../resources/intentionallyblankpage.com';
        $contents = file_get_contents($filename);

        $headers = $parser->parseHeaders($contents);
        $this->assertArrayHasKey($key = 0, $headers);
    }

    public function testParseHeadersRedirectToBlankPage()
    {
        $parser = new MKJHeaderParser();

        $filename = __DIR__ . '/../../resources/redirect-to-intentionallyblankpage.com';
        $contents = file_get_contents($filename);

        $headers = $parser->parseHeaders($contents);
        $count = count($headers);
        $this->assertEquals($expected = 2, $count);
    }

    public function testParseHeadersSplitsOnTheFirstColonOnly()
    {
        $parser = new MKJHeaderParser();

        $content = "HTTP/1.1 200 OK\r\n"
            . "Date: Wed, 14 Jun 2017 06:26:57 GMT\r\n"
            . "Content-Security-Policy: default-src 'self'; report-to: csp\r\n"
            . "Server:nginx\r\n\r\n";

        $headers = $parser->parseHeaders($content);

        $this->assertSame('HTTP/1.1 200 OK', $headers[0][0]);
        $this->assertSame('Wed, 14 Jun 2017 06:26:57 GMT', $headers[0]['Date']);
        $this->assertSame("default-src 'self'; report-to: csp", $headers[0]['Content-Security-Policy']);
        $this->assertSame('nginx', $headers[0]['Server']);
    }

    public function testParseHeadersToleratesALineWithoutAColon()
    {
        $parser = new MKJHeaderParser();

        $content = "HTTP/1.1 200 OK\r\nbroken\r\n\r\n";

        $headers = $parser->parseHeaders($content);

        $this->assertSame('', $headers[0]['broken']);
    }
}
