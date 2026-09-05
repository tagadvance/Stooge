<?php

namespace tagadvance\stooge;

/**
 * The outcome of a completed {@see CurlRequest::execute()}: the final status
 * code, the headers of every hop, and the body.
 *
 * @author Tag <tagadvance+stooge@gmail.com>
 * @see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Response_message
 */
class CurlResponse
{
    private $statusCode;

    private $body;

    private $headers = [];

    /**
     * @param array<int, array<int|string, string>> $headers
     *            One header map per hop, in request order, as produced by
     *            {@see HeaderParser::parseHeaders()}.
     */
    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * The status of the final hop; the codes of any redirects that were followed
     * are not reported here.
     */
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

    /**
     * The response body, or the string "1" when the request ran without
     * `CURLOPT_RETURNTRANSFER` — see {@see CurlRequest::execute()}.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Decodes the body as JSON of any shape, raising an `E_USER_WARNING` when the
     * Content-Type is not `application/json`. Returns null both for a literal `null` body
     * and for one that is not valid JSON at all.
     */
    public function getDecodedBody(): mixed
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

    /**
     * The body decoded as a JSON object. A body whose top level is an array or a scalar is
     * valid JSON but not an object, so it is rejected here rather than raising a TypeError
     * on the way out; reach for getDecodedBody() when the shape is not known to be an object.
     *
     * @throws CurlException when the decoded top level is not an object.
     */
    public function getBodyAsJson(): ?\stdClass
    {
        $decoded = $this->getDecodedBody();
        if ($decoded !== null && ! $decoded instanceof \stdClass) {
            throw new CurlException('JSON body is not an object; use getDecodedBody()');
        }

        return $decoded;
    }

    /**
     * A boxed dump of every hop's headers and of the body, for reading in a
     * terminal rather than for reconstructing the response.
     */
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
