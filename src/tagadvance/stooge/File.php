<?php

namespace tagadvance\stooge;

/**
 *
 * @author Tag <tagadvance+stooge@gmail.com>
 *        
 */
class File {
	
	/**
	 * Hidden constructor
	 */
	private function __construct() {
	}
	
	static function createTempFile($fileName, $directory = null): \SplFileInfo {
		if ($directory === null) {
			$directory = sys_get_temp_dir ();
		}
		
		$temp = tempnam ( $directory, $fileName );
		if ($temp === false) {
			$message = 'temporary file could not be created';
			throw new CurlException ( $message );
		}
		
		$deleteOnExit = function () use ($temp) {
			unlink ( $temp );
		};
		register_shutdown_function ( $deleteOnExit );
		
		return new \SplFileInfo ( $temp );
	}
	
}