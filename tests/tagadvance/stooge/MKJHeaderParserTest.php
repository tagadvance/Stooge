<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class MKJHeaderParserTest extends TestCase {
	
	function testParseHeaders() {
		$parser = new MKJHeaderParser ();
		
		$filename = __DIR__ . '/../../resources/intentionallyblankpage';
		$contents = file_get_contents ( $filename );
		
		$headers = $parser->parseHeaders ( $contents );
		$this->assertArrayHasKey ( $key = 0, $headers );
	}
	
}