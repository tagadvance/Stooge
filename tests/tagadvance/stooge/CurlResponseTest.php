<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class CurlResponseTest extends TestCase
{
    /**
     * @return array<int, array<int|string, string>>
     */
    private function redirectHeaders(): array
    {
        return [
            [
                0 => 'HTTP/1.1 302 Found',
                'Location' => 'http://intentionallyblankpage.com',
                'Content-Type' => 'text/html; charset=UTF-8',
            ],
            [
                0 => 'HTTP/1.1 200 OK',
                'Content-Type' => MimeType::JSON,
            ],
        ];
    }

    public function testGetHeaderReturnsTheFinalHopValue()
    {
        $response = new CurlResponse(200, $this->redirectHeaders(), '');

        $this->assertSame(MimeType::JSON, $response->getHeader('Content-Type'));
    }

    public function testGetHeaderIsCaseInsensitive()
    {
        $response = new CurlResponse(200, $this->redirectHeaders(), '');

        $this->assertSame(MimeType::JSON, $response->getHeader('content-type'));
    }

    public function testGetHeaderReturnsNullWhenAbsent()
    {
        $response = new CurlResponse(200, $this->redirectHeaders(), '');

        // set by the first hop only
        $this->assertNull($response->getHeader('Location'));
        $this->assertNull($response->getHeader('X-Nonexistent'));
    }

    public function testGetHeaderReturnsNullWhenThereAreNoHeaders()
    {
        $response = new CurlResponse(200, [], '');

        $this->assertNull($response->getHeader('Content-Type'));
    }

    public function testGetBodyAsJsonDoesNotWarnForAJsonContentType()
    {
        $response = new CurlResponse(200, $this->redirectHeaders(), '{"foo":"bar"}');

        $warnings = [];
        set_error_handler(function (int $errno, string $message) use (&$warnings): bool {
            $warnings[] = $message;
            return true;
        }, E_USER_WARNING);
        try {
            $json = $response->getBodyAsJson();
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $warnings);
        $this->assertSame('bar', $json->foo);
    }

    public function testGetBodyAsJsonWarnsForAnUnexpectedContentType()
    {
        $headers = [
            [
                0 => 'HTTP/1.1 200 OK',
                'Content-Type' => MimeType::HTML,
            ],
        ];
        $response = new CurlResponse(200, $headers, '{}');

        $warnings = [];
        set_error_handler(function (int $errno, string $message) use (&$warnings): bool {
            $warnings[] = $message;
            return true;
        }, E_USER_WARNING);
        try {
            $response->getBodyAsJson();
        } finally {
            restore_error_handler();
        }

        $this->assertSame(['unexpected Content-Type: ' . MimeType::HTML], $warnings);
    }

    public function testToStringRendersEveryHop()
    {
        $response = new CurlResponse(200, $this->redirectHeaders(), 'body');

        $string = (string) $response;

        $this->assertStringContainsString('| HTTP/1.1 302 Found', $string);
        $this->assertStringContainsString('| HTTP/1.1 200 OK', $string);
        $this->assertStringContainsString('| Location: http://intentionallyblankpage.com', $string);
        $this->assertStringContainsString('| body', $string);
    }

    public function testGetBodyAsJsonIgnoresContentTypeParameters()
    {
        $headers = [
            [
                0 => 'HTTP/1.1 200 OK',
                'Content-Type' => 'Application/JSON; charset=utf-8',
            ],
        ];
        $response = new CurlResponse(200, $headers, '{"foo":"bar"}');

        $warnings = [];
        set_error_handler(function (int $errno, string $message) use (&$warnings): bool {
            $warnings[] = $message;
            return true;
        }, E_USER_WARNING);
        try {
            $json = $response->getBodyAsJson();
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $warnings);
        $this->assertSame('bar', $json->foo);
    }

    public function testGetBodyAsJsonRejectsANonObjectTopLevel()
    {
        $response = new CurlResponse(200, [['content-type' => 'application/json']], '[1,2,3]');

        $this->expectException(CurlException::class);
        $this->expectExceptionMessage('JSON body is not an object');
        $response->getBodyAsJson();
    }

    public function testGetDecodedBodyReturnsANonObjectTopLevel()
    {
        $response = new CurlResponse(200, [['content-type' => 'application/json']], '[1,2,3]');

        $this->assertSame([1, 2, 3], $response->getDecodedBody());
    }
}
