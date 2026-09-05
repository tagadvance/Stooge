<?php

namespace tagadvance\stooge;

/**
 *
 * @author Tag <tagadvance+stooge@gmail.com>
 * @see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Response_message
 */
class CurlResponse
{
    private $statusCode;

    private $body;

    private $headers = [];

    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    public function getCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Look up a header of the final hop, i.e. what the caller means by "the
     * response headers" once redirects have been followed. Header names are
     * case insensitive.
     */
    public function getHeader(string $name): ?string
    {
        $headers = $this->getLastHopHeaders();
        return $headers[strtolower($name)] ?? null;
    }

    /**
     * $headers is a list of per-hop header maps, one entry per redirect.
     *
     * @return array<string, string> the final hop's headers, keyed by lowercase name
     */
    private function getLastHopHeaders(): array
    {
        $hop = end($this->headers);
        if (! is_array($hop)) {
            return [];
        }

        $headers = [];
        foreach ($hop as $name => $value) {
            // the status line is stored under a numeric key
            if (is_string($name)) {
                $headers[strtolower($name)] = $value;
            }
        }
        return $headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getBodyAsJson(): ?\stdClass
    {
        $contentType = $this->getHeader('Content-Type') ?? '';
        // e.g. "application/json; charset=utf-8"
        $mediaType = strtolower(trim(explode(';', $contentType, 2)[0]));
        if ($mediaType !== MimeType::JSON) {
            $message = "unexpected Content-Type: $contentType";
            trigger_error($message, E_USER_WARNING);
        }
        return json_decode($this->body);
    }

    public function __toString()
    {
        $headerData = '';
        foreach ($this->headers as $headers) {
            foreach ($headers as $name => $value) {
                // i.e. $headerData does not end with PHP_EOL
                if (! empty($headerData) && strpos($headerData, PHP_EOL, - strlen(PHP_EOL)) === false) {
                    $headerData .= PHP_EOL;
                }

                if (is_numeric($name)) {
                    $headerData .= "| $value";
                } else {
                    $headerData .= "| $name: $value";
                }
            }
        }

        $body = '';
        $lines = explode($delimiter = PHP_EOL, $this->body);
        foreach ($lines as $line) {
            if (! empty($body)) {
                $body .= PHP_EOL;
            }
            $body .= "| $line";
        }

        return <<<RESPONSE
┌─────────────────────────
$headerData
|
$body
└─────────────────────────
RESPONSE;
    }

}
