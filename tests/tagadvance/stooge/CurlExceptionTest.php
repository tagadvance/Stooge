<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class CurlExceptionTest extends TestCase {
	
	function testGetErrorMessage() {
		$message = 'foo';
		$code = 0;
		$e = new CurlException ( $message, $code );
		$errorMessage = $e->getErrorMessage ();
		
		$condition = is_string ( $errorMessage );
		$this->assertTrue ( $condition );
	}
	
}