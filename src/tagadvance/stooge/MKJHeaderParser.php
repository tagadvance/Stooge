<?php

namespace tagadvance\stooge;

/**
 * Splits libcurl's header block on blank lines, so a redirect chain yields one
 * map per hop.
 *
 * @see https://stackoverflow.com/a/18682872/625688
 */
class MKJHeaderParser implements HeaderParser
{
    /**
     * @return array<int, array<int|string, string>> one map per hop, in request order
     */
    public function parseHeaders(string $content): array
    {
        $headers = [];

        $pattern = "/(\r?\n){2}/";
        $requests = preg_split($pattern, $content);

        foreach ($requests as $request) {
            $request = trim($request);
            if (! empty($request)) {
                $headers[] = $this->parseRequestHeaders($request);
            }
        }

        return $headers;
    }

    /**
     * A field repeated within one hop collapses to its last occurrence.
     *
     * @return array<int|string, string> the status line under the numeric key 0,
     *         each field under its name
     */
    private function parseRequestHeaders($request)
    {
        $headers = [];

        $pattern = "/(\r?\n)/";
        $lines = preg_split($pattern, $request);
        foreach ($lines as $i => $line) {
            if ($i === 0) {
                $headers[] = $line;
            } else {
                // split on the first colon only; a value may contain more
                list($key, $value) = array_pad(explode(':', $line, 2), 2, '');
                $headers[trim($key)] = trim($value);
            }
        }

        return $headers;
    }

}
