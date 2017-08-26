<?php

namespace tagadvance\stooge;

/**
 *
 * @see https://stackoverflow.com/a/18682872/625688
 */
class MKJHeaderParser implements HeaderParser {

    const NEWLINE = "\r\n";

    function parseHeaders(string $content): array {
        $headers = [];
        
        $pattern = "/(\r\n|\n){2}/";
        $requests = preg_split($pattern, $content);
        
        foreach ($requests as $request) {
            $request = trim($request);
            if (! empty($request)) {
                $headers[] = $this->parseRequestHeaders($request);
            }
        }
        
        return $headers;
    }

    private function parseRequestHeaders($request) {
        $headers = [];
        
        $pattern = "/(\r\n|\n)/";
        $lines = preg_split($pattern, $request);
        foreach ($lines as $i => $line) {
            if ($i === 0) {
                $headers[] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }
        
        return $headers;
    }

}