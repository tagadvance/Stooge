<?php

namespace tagadvance\stooge;

/**
 *
 * @author Tag <tagadvance+stooge@gmail.com>
 *        
 */
interface HeaderParser {

    function parseHeaders(string $content): array;

}