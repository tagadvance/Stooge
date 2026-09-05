<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class URLQueryTest extends TestCase
{
    public function testCreateFromQueryStringIsParsed()
    {
        $query = '?foo=bar&a=apple&b=banana';
        $urlQuery = URLQuery::createFromQueryString($query);
        $this->assertEquals($expected = 'bar', $actual = $urlQuery->foo);
        $this->assertEquals($expected = 'apple', $actual = $urlQuery->a);
        $this->assertEquals($expected = 'banana', $actual = $urlQuery->b);
    }

    public function testCreateFromQueryStringIsDecoded()
    {
        $query = '?space=+%20+';
        $urlQuery = URLQuery::createFromQueryString($query);
        $this->assertEquals($expected = '   ', $actual = $urlQuery->space);
    }

    public function testMagicIsSetAndUnset()
    {
        $parameters = [];
        $urlQuery = new URLQuery($parameters);

        $condition = ! isset($urlQuery->foo);
        $this->assertTrue($condition);

        $urlQuery->foo = 'bar';
        $condition = isset($urlQuery->foo);
        $this->assertTrue($condition);

        unset($urlQuery->foo);
        $condition = ! isset($urlQuery->foo);
        $this->assertTrue($condition);
    }

    public function testMagicGetAndSet()
    {
        $parameters = [];
        $urlQuery = new URLQuery($parameters);

        $expected = 'bar';
        $urlQuery->foo = $expected;

        $this->assertEquals($expected, $actual = $urlQuery->foo);
    }

    public function testToString()
    {
        $expected = '?foo=test&bar=test';

        $parameters = [
            'foo' => 'test',
            'bar' => 'test',
        ];
        $urlQuery = new URLQuery($parameters);
        $string = $urlQuery->toString();

        $this->assertEquals($expected, $string);
    }

    public function testCreateFromQueryStringKeepsSeparatorsInValues()
    {
        $query = '?token=YWJjZA==&next=' . urlencode('/a?b=c');
        $urlQuery = URLQuery::createFromQueryString($query);

        $this->assertSame('YWJjZA==', $urlQuery->token);
        $this->assertSame('/a?b=c', $urlQuery->next);
    }

    public function testCreateFromQueryStringHandlesAPairWithoutAValue()
    {
        $query = '?flag&foo=bar';
        $urlQuery = URLQuery::createFromQueryString($query);

        $this->assertSame('', $urlQuery->flag);
        $this->assertSame('bar', $urlQuery->foo);
    }

    public function testCreateFromQueryStringHandlesAnEmptyQuery()
    {
        $urlQuery = URLQuery::createFromQueryString('?');

        $this->assertSame('?', $urlQuery->toString());
    }

    public function testGetReturnsNullForAnAbsentParameter()
    {
        $query = new URLQuery([]);

        set_error_handler(static function (int $errno, string $error): bool {
            throw new \ErrorException($error, 0, $errno);
        });
        try {
            $this->assertNull($query->absent);
        } finally {
            restore_error_handler();
        }
    }
}
