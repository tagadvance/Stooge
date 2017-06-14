<?php

namespace tagadvance\stooge;

/**
 * 
 * @author Tag <tagadvance+stooge@gmail.com>
 * @see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Response_message
 */
class CurlResponse {
	
	private $statusCode;
	private $body;
	private $headers = [ ];
	
	function __construct(int $statusCode, array $headers, string $body) {
		$this->statusCode = $statusCode;
		$this->body = $body;
		$this->headers = $headers;
	}
	
	function getCode(): int {
		return $this->statusCode;
	}
	
	function getHeader($name): string {
		return $this->headers [$name];
	}
	
	function getBody(): string {
		return $this->body;
	}
	
	function getBodyAsJson(): \stdClass {
		if ($this->headers ['content-type'] != MimeType::JSON) {
			throw new \CurlException ( $this->body, $code = 0 );
		}
		return json_decode ( $this->body );
	}
	
	function __toString() {
		$headerData = '';
		foreach ( $this->headers as $headers ) {
			foreach ( $headers as $name => $value ) {
				// i.e. $headerData does not end with PHP_EOL
				if (! empty ( $headerData ) && strpos ( $headerData, PHP_EOL, - strlen ( PHP_EOL ) ) === false) {
					$headerData .= PHP_EOL;
				}
				
				if (is_numeric ( $name )) {
					$headerData .= "| $value";
				} else {
					$headerData .= "| $name: $value";
				}
			}
		}
		
		$body = '';
		$lines = explode ( $delimiter = PHP_EOL, $this->body );
		foreach ( $lines as $line ) {
			if (! empty ( $body )) {
				$body .= PHP_EOL;
			}
			$body .= "| $line";
		}
		
		return <<<RESPONSE
┌─────────────────────────
$headerData
|
$body
└─────────────────────────
RESPONSE;
	}
		
}