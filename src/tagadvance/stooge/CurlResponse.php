<?php

namespace tagadvance\stooge;

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
			$headerData .= PHP_EOL;
			foreach ( $headers as $name => $value ) {
				// i.e. $headerData does not end with PHP_EOL
				if (strpos ( $headerData, PHP_EOL, - strlen ( PHP_EOL ) ) === false) {
					$headerData .= PHP_EOL;
				}
				
				if (is_numeric ( $name )) {
					$headerData .= "|\t$value";
				} else {
					$headerData .= "|\t$name: $value";
				}
			}
		}
		
		$body = '';
		$lines = explode ( $delimiter = PHP_EOL, $this->body );
		foreach ( $lines as $line ) {
			$body .= PHP_EOL . "|$line";
		}
		
		return <<<RESPONSE
┌──────────────────────────────
| HTTP Status Code: $this->statusCode
| Headers: $headerData
| Body: $body
└──────────────────────────────
RESPONSE;
	}
		
}