<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class URLQueryTest extends TestCase {
	
	function testCreateFromQueryStringIsParsed() {
		$query = '?foo=bar&a=apple&b=banana';
		$urlQuery = URLQuery::createFromQueryString($query);
		$this->assertEquals($expected = 'bar', $actual = $urlQuery->foo);
		$this->assertEquals($expected = 'apple', $actual = $urlQuery->a);
		$this->assertEquals($expected = 'banana', $actual = $urlQuery->b);
	}
	
	function testCreateFromQueryStringIsDecoded() {
		$query = '?space=+%20+';
		$urlQuery = URLQuery::createFromQueryString($query);
		$this->assertEquals($expected = '   ', $actual = $urlQuery->space);
	}
	
	function testMagicIsSetAndUnset() {
		$parameters = [];
		$urlQuery = new URLQuery($parameters);
		
		$condition = !isset($urlQuery->foo);
		$this->assertTrue($condition);

		$urlQuery->foo = 'bar';
		$condition = isset($urlQuery->foo);
		$this->assertTrue($condition);
		
		unset($urlQuery->foo);
		$condition = !isset($urlQuery->foo);
		$this->assertTrue($condition);
	}
	
	function testMagicGetAndSet() {
		$parameters = [];
		$urlQuery = new URLQuery($parameters);
		
		$expected = 'bar';
		$urlQuery->foo = $expected;
		
		$this->assertEquals($expected, $actual = $urlQuery->foo);
	}
	
	function testToString() {
		$expected = '?foo=test&bar=test';
		
		$parameters = [ 
				'foo' => 'test',
				'bar' => 'test'
		];
		$urlQuery = new URLQuery ( $parameters );
		$string = $urlQuery->toString ();
		
		$this->assertEquals ( $expected, $string );
	}
	
}