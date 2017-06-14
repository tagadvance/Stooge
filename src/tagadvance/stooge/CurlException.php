<?php

namespace tagadvance\stooge;

/**
 *
 * @author Tag <tagadvance+stooge@gmail.com>
 *        
 */
class CurlException extends \Exception {
	
	/**
	 *
	 * @param string $message        	
	 * @param int $code
	 *        	One of the <a href="https://curl.haxx.se/libcurl/c/libcurl-errors.html">cURL error code</a> constants.
	 * @param Exception $previous        	
	 */
	function __construct($message, $code, Exception $previous = null) {
		parent::__construct ( $message, $code, $previous );
	}
	
	/**
	 * 
	 * @return string
	 */
	function getErrorMessage(): string {
		$code = $this->getCode ();
		return curl_strerror ( $code );
	}
	
}