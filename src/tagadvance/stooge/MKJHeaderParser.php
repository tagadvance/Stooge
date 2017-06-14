<?php

namespace tagadvance\stooge;

/**
 *
 * 
 * @see https://stackoverflow.com/a/18682872/625688
 */
class MKJHeaderParser {
	
	function parseHeaders(string $content): array {
		$headers = array ();
		
		// Split the string on every "double" new line.
		$arrRequests = explode ( "\r\n\r\n", $content);
		
		// Loop of response headers. The "count() -1" is to
		// avoid an empty row for the extra line break before the body of the response.
		for($index = 0; $index < count ( $arrRequests ) - 1; $index ++) {
			
			foreach ( explode ( "\r\n", $arrRequests [$index] ) as $i => $line ) {
				if ($i === 0)
					$headers [$index] ['http_code'] = $line;
				else {
					list ( $key, $value ) = explode ( ': ', $line );
					$headers [$index] [$key] = $value;
				}
			}
		}
		
		return $headers;
	}
	
}