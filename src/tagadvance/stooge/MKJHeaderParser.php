<?php

namespace tagadvance\stooge;

/**
 *
 * 
 * @see https://stackoverflow.com/a/18682872/625688
 */
class MKJHeaderParser {
	
	const NEWLINE = "\r\n";
	
	function parseHeaders(string $content): array {
		$headers = [ ];
		
		$doubleNewline = self::NEWLINE . self::NEWLINE;
		$requests = explode ( $doubleNewline, $content );
		
		foreach ( $requests as $request ) {
			$request = trim ( $request );
			if (! empty ( $request )) {
				$headers [] = $this->parseRequestHeaders ( $request );
			}
		}
		
		return $headers;
	}
	
	private function parseRequestHeaders($request) {
		$headers = [ ];
		
		$lines = explode ( self::NEWLINE, $request );
		foreach ( $lines as $i => $line ) {
			if ($i === 0) {
				$headers [] = $line;
			} else {
				list ( $key, $value ) = explode ( ': ', $line );
				$headers [$key] = $value;
			}
		}
		
		return $headers;
	}
	
}