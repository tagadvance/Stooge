<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class HttpStatusCodeTest extends TestCase {
	
	function testValueOf() {
		$value = HttpStatusCode::valueOf ( HttpStatusCode::OK );
		$this->assertEquals ( $expected = 'OK', $value );
		
		$value = HttpStatusCode::valueOf ( WebDAV::PROCESSING );
		$this->assertEquals ( $expected = 'PROCESSING', $value );
	}
	
}