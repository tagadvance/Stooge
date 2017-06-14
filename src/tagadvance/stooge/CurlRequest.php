<?php

namespace tagadvance\stooge;

define ( 'USER_AGENT_CHROME', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36' );

/**
 * @author Tag <tagadvance+stooge@gmail.com>
 * @see http://www.php.net/manual/en/intro.curl.php
 */
class CurlRequest {
	
	/**
	 * 
	 * @var resource
	 */
	private $curlSession;
	/**
	 * 
	 * @var array
	 */
	private $options = [ ];
	
	/**
	 *
	 * @see http://www.php.net/manual/en/function.curl-init.php
	 */
	function __construct() {
		$this->curlSession = curl_init ();
	}
	
	/**
	 *
	 * @return resource
	 */
	function getCurlSession() {
		return $this->curlSession;
	}
	
	/**
	 *
	 * @return \tagadvance\stooge\CurlRequest
	 * @see http://www.php.net/manual/en/function.curl-copy-handle.php
	 */
	function __clone() {
		$this->curlSession = curl_copy_handle ( $this->curlSession );
		// TODO: does $options need to be copied too?
	}
	
	/**
	 *
	 * @return self
	 * @throws CurlException
	 */
	function autoDetectUserAgent(): self {
		$agent = $_SERVER ['HTTP_REFERER'] ?? USER_AGENT_CHROME;
		$this->setOption ( CURLOPT_USERAGENT, $agent );
		return $this;
	}
	
	/**
	 *
	 * @param string $string
	 *        	The string to be encoded.
	 * @throws CurlException
	 * @see http://www.php.net/manual/en/function.curl-escape.php
	 */
	function escape($string): string {
		$result = curl_escape ( $this->curlSession, $string );
		if ($result === false) {
			throw new CurlException ( __METHOD__ . "($string)" );
		}
		return $result;
	}
	
	/**
	 *
	 * @param string $string
	 *        	The URL encoded string to be decoded.
	 * @throws CurlException
	 * @see http://www.php.net/manual/en/function.curl-unescape.php
	 */
	function unescape($string): string {
		$result = curl_unescape ( $this->curlSession, $string );
		if ($result === false) {
			throw new CurlException ( __METHOD__ . "($string)" );
		}
		return $result;
	}
	
	/**
	 * Cause this curl session to use a temporary cookie file which is
	 * automatically deleted when the script ends.
	 *
	 * @return self
	 * @throws CurlException
	 */
	function autoCookieJar() {
		$cookiePath = File::createTempFile ( 'COOKIE' )->getRealPath ();
		$this->setOptions ( [ 
				CURLOPT_COOKIEJAR => $cookiePath,
				CURLOPT_COOKIEFILE => $cookiePath 
		] );
		return $this;
	}
	
	/**
	 * 
	 * @param unknown $name
	 * @return mixed
	 */
	function __get($name) {
		return $this->getOption ( $name );
	}
	
	/**
	 * 
	 * @param unknown $name
	 * @param unknown $value
	 */
	function __set($name, $value) {
		$magicOption = $this->magicOption ( $name );
		$this->setOption ( $magicOption, $value );
	}
	
	/**
	 *
	 * @param string $name        	
	 * @param array $arguments        	
	 * @throws \BadMethodCallException
	 */
	function __call(string $name, array $arguments) {
		$nameStartsWithSet = strpos ( $name, $needle = 'set' ) === 0;
		$count = count ( $arguments );
		
		switch($count) {
			case 0:
				$value = true;
				break;
			case 1:
				$value = array_shift ( $arguments );
				break;
		}
		
		if ($nameStartsWithSet && isset($value)) {
			$setter = substr ( $name, $start = strlen ( $needle ) );
			
			// https://stackoverflow.com/a/19533226/625688
			$underscore = preg_replace ( '/(?<!^)[A-Z]/', '_$0', $setter );
			$upper = strtoupper ( $underscore );
			$this->$upper = $value;
			return $this;
		}
		
		$message = "$name(...)";
		throw new \BadMethodCallException ( $message );
	}
	
	/**
	 * 
	 * @param unknown $option
	 * @return unknown
	 */
	function __isset($option) {
		$magicOption = $this->magicOption ( $option );
		return isset ( $this->options [$magicOption] );
	}
	
	/**
	 * 
	 * @param unknown $option
	 * @throws \RuntimeException
	 */
	function __unset($option) {
		$message = "unsupported operation: __unset($option)";
		throw new \RuntimeException ( $message );
	}
	
	/**
	 * 
	 * @param unknown $option
	 * @return mixed
	 */
	function getOption($option) {
		$magicOption = $this->magicOption ( $option );
		return $this->options [$magicOption];
	}
	
	/**
	 * 
	 * @param unknown $option
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	protected function magicOption($option) {
		if (defined ( $option )) {
			return constant ( $option );
		}
		
		$prefixes = [ 
				'CURLOPT_',
				'CURLINFO_',
				'CURLMOPT_',
				'CURLSSH_',
				'CURLSSLOPT_',
				'CURL_' 
		];
		foreach ( $prefixes as $prefix ) {
			$curlopt = $prefix . strtoupper ( $option );
			if (defined ( $curlopt )) {
				return constant ( $curlopt );
			}
		}
		
		throw new \InvalidArgumentException ( $option );
	}
	
	/**
	 *
	 * @param unknown $option        	
	 * @param unknown $value        	
	 * @throws CurlException
	 * @return self
	 * @see http://php.net/curl_setopt
	 */
	function setOption($option, $value): self {
		$this->options [$option] = $value;
		$result = curl_setopt ( $this->curlSession, $option, $value );
		if ($result === false) {
			throw new CurlException ( "option $option could not be set" );
		}
		return $this;
	}
	
	/**
	 *
	 * @param array $options        	
	 * @return self
	 */
	function setOptions(array $options): self {
		// curl_setopt_array($this->session, $options);
		// this way we get a useful message in the event of an exception
		foreach ( $options as $option => $value ) {
			$this->setOption ( $option, $value );
		}
		return $this;
	}
	
	/**
	 *
	 * @param string $url        	
	 * @return \tagadvance\stooge\CurlResponse
	 */
	function get(string $url): CurlResponse {
		return $this->setUrl ( $url )->execute ();
	}
	
	/**
	 *
	 * @param mixed $fields        	
	 * @return self
	 */
	function post($fields): CurlResponse {
		return $this->setOptions ( [ 
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $fields 
		] )->execute ();
	}
	
	/**
	 *
	 * @param mixed $fields        	
	 * @return self
	 */
	function put($fields): CurlResponse {
		return $this->setOptions ( [ 
				CURLOPT_PUT => true,
				CURLOPT_POSTFIELDS => $fields 
		] )->execute ();
	}
	
	/**
	 * 
	 * @return \tagadvance\stooge\CurlResponse
	 */
	function __invoke() {
		return $this->execute ();
	}
	
	/**
	 * TODO: add proxy support
	 * http://stackoverflow.com/questions/9183178/php-curl-retrieving-response-headers-and-body-in-a-single-request
	 * 
	 * @return CurlResponse
	 */
	function execute(): CurlResponse {
		$callback = $this->HEADERFUNCTION ?? function ($curlResource, $headerData) {
			return strlen ( $headerData );
		};
		$headerText = '';
		$this->HEADERFUNCTION = function ($curlResource, $headerData) use ($callback, &$headerText) {
			$headerText .= $headerData;
			return $callback ( $curlResource, $headerData );
		};
		
		$result = $this->rawExec ();
		
		$parser = new MKJHeaderParser ();
		$headers = $parser->parseHeaders ( $headerText );
		
		if (isset ( $this->CURLOPT_HEADER ) && $this->CURLOPT_HEADER) {
			$headerSize = curl_getinfo ( $this->curlSession, CURLINFO_HEADER_SIZE );
			$result = substr ( $result, $headerSize );
		}
		
		$code = $this->getInformation(CURLINFO_HTTP_CODE);
		return new CurlResponse ( $code, $headers, $result );
	}
	
	/**
	 * Return raw, unprocessed result.
	 * 
	 * @throws CurlException
	 * @return mixed
	 */
	function rawExec() {
		$result = curl_exec ( $this->curlSession );
		if ($result === false) {
			$message = curl_error ( $this->curlSession );
			$code = curl_errno ( $this->curlSession );
			throw new CurlException ( $message, $code );
		}
		return $result;
	}
	
	/**
	 *
	 * @return self
	 * @see http://php.net/manual/en/function.curl-reset.php
	 */
	function reset(): self {
		curl_reset ( $this->curlSession );
		return $this;
	}
	
	/**
	 *
	 * @return self
	 * @see http://php.net/manual/en/function.curl-close.php
	 */
	function close(): self {
		curl_close ( $this->curlSession );
		return $this;
	}
	
	/**
	 *
	 * @param integer $option        	
	 * @see http://www.php.net/manual/en/function.curl-getinfo.php
	 */
	function getInformation($option = null) {
		return curl_getinfo ( $this->curlSession, $option );
	}
	
	/**
	 *
	 * @param integer $age        	
	 * @return array
	 * @see http://www.php.net/manual/en/function.curl-version.php
	 */
	static function version($age = CURLVERSION_NOW): array {
		return curl_version ( $age );
	}
	
}