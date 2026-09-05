<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\Attributes\DataProvider;
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

    /**
     * @return array<string, array{string}>
     */
    public static function unforwardableUserAgentProvider(): array
    {
        return [
            'carriage return and line feed' => ["Mozilla/5.0\r\nX-Injected: yes"],
            'line feed alone' => ["Mozilla/5.0\nX-Injected: yes"],
            'carriage return alone' => ["Mozilla/5.0\rX-Injected: yes"],
            'null byte' => ["Mozilla/5.0\0"],
            'longer than the cap' => [str_repeat('A', 1025)],
            'empty' => [''],
        ];
    }

    #[DataProvider('unforwardableUserAgentProvider')]
    public function testAutoDetectUserAgentRejectsAnUnforwardableInboundHeader(string $agent)
    {
        $server = $_SERVER;
        $_SERVER['HTTP_USER_AGENT'] = $agent;
        try {
            $request = new CurlRequest();
            $request->autoDetectUserAgent();

            $this->assertSame(USER_AGENT_CHROME, $request->getOption('USERAGENT'));
        } finally {
            $_SERVER = $server;
        }
    }

    public function testAutoDetectUserAgentForwardsAUserAgentAtTheCap()
    {
        $server = $_SERVER;
        $_SERVER['HTTP_USER_AGENT'] = $agent = str_repeat('A', 1024);
        try {
            $request = new CurlRequest();
            $request->autoDetectUserAgent();

            $this->assertSame($agent, $request->getOption('USERAGENT'));
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

    public function testGetOptionAcceptsACurlOptionConstant()
    {
        $request = new CurlRequest();
        $request->setOption(CURLOPT_USERAGENT, 'Test/1.0');

        $this->assertSame('Test/1.0', $request->getOption(CURLOPT_USERAGENT));
    }

    public function testGetOptionReturnsNullForAnOptionNeverSet()
    {
        $request = new CurlRequest();

        set_error_handler(static function (int $errno, string $error): bool {
            throw new \ErrorException($error, 0, $errno);
        });
        try {
            $this->assertNull($request->getOption(CURLOPT_RETURNTRANSFER));
        } finally {
            restore_error_handler();
        }
    }

    public function testExecuteReturnsTheBodyWithoutTheCallerSettingReturntransfer()
    {
        $path = tempnam(sys_get_temp_dir(), 'stooge');
        file_put_contents($path, $expected = 'hello from a file');
        try {
            $request = new CurlRequest();
            $response = $request->get("file://$path");

            $this->assertSame($expected, $response->getBody());
        } finally {
            unlink($path);
        }
    }
}
