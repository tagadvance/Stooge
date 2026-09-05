<?php

namespace tagadvance\stooge;

/**
 * Parses the raw header block libcurl hands to `CURLOPT_HEADERFUNCTION`, which
 * carries one header section per hop whenever redirects were followed.
 *
 * @author Tag <tagadvance+stooge@gmail.com>
 */
interface HeaderParser
{
    /**
     * @return array<int, array<int|string, string>> one map per hop, in request
     *         order; within a hop the status line sits under the numeric key 0
     *         and each field under its name as sent
     */
    public function parseHeaders(string $content): array;

}
