<?php

namespace tagadvance\stooge;

/**
 * Thrown when a libcurl call fails. The exception code is a libcurl error
 * number, not an HTTP status code.
 *
 * @author Tag <tagadvance+stooge@gmail.com>
 */
class CurlException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     *            One of the <a href="https://curl.haxx.se/libcurl/c/libcurl-errors.html">cURL error code</a> constants.
     */
    public function __construct($message, $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * libcurl's own description of this exception's code. Instances raised for
     * failures libcurl did not report keep the default code 0, for which this
     * returns "No error".
     */
    public function getErrorMessage(): string
    {
        $code = $this->getCode();
        return curl_strerror($code);
    }

}
