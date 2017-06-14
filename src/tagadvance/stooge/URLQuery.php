<?php

namespace tagadvance\stooge;

class URLQuery {
	
	private $parameters;
	
	static function createFromQueryString(string $query, $prefix = '?', $keyValuePairSeparator = '=', $delimiter = '&') {
		$parameters = [ ];
		
		if (strpos ( $query, $prefix ) === 0) {
			$query = substr ( $query, $start = strlen ( $prefix ) );
		}
		
		$keyValuePairs = explode ( $delimiter, $query );
		foreach ( $keyValuePairs as $pair ) {
			list ( $key, $value ) = explode ( $keyValuePairSeparator, $pair );
			$decodedKey = urldecode ( $key );
			$decodedValue = urldecode ( $value );
			$parameters [$decodedKey] = $decodedValue;
		}
		
		return new self ( $parameters );
	}
	
	function __construct(array $parameters) {
		$this->parameters = $parameters;
	}
	
	function __get(string $name) {
		return $this->parameters [$name];
	}
	
	function __set(string $name, $value) {
		return $this->parameters [$name] = $value;
	}
	
	function __isset(string $name) {
		return isset ( $this->parameters [$name] );
	}
	
	function __unset(string $name) {
		unset ( $this->parameters [$name] );
	}
	
	function __toString() {
		return $this->toString();
	}
	
	function toString($prefix = '?', $keyValuePairSeparator = '=', $delimiter = '&') {
		$string = $prefix;
		foreach ( $map as $key => $value ) {
			if (strlen ( $string ) > strlen ( $prefix )) {
				$sb .= $delimiter;
			}
			$encodedKey = urlencode ( $key );
			$encodedValue = urlencode ( $value );
			$string .= "$encodedKey$keyValuePairSeparator$encodedValue";
		}
		return $string;
	}
	
	function __sleep() {
		return [ 
				'parameters' 
		];
	}
	
}