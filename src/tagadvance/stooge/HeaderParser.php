<?php

namespace tagadvance\stooge;

/**
 *
 * @author Tag <tagadvance@gmail.com>
 *        
 */
interface HeaderParser {
	
	function parseHeaders(string $content): array;
	
}