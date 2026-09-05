<?php

namespace tagadvance\stooge;

define('USER_AGENT_CHROME', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');

/**
 * Any `setFoo(...)` call is routed through {@see CurlRequest::__call()} to the
 * matching `CURLOPT_FOO` option, and any `$request->CURLOPT_FOO` access through
 * {@see CurlRequest::__get()}. Only the ones this class uses on itself are
 * annotated below.
 *
 * @method self setUrl(string $url)
 * @property mixed $HEADERFUNCTION
 * @property mixed $CURLOPT_HEADER
 * @author Tag <tagadvance+stooge@gmail.com>
 * @see http://www.php.net/manual/en/intro.curl.php
 */
class CurlRequest
{
    private \CurlHandle $curlSession;

    /**
     *
     * @var array
     */
    private $options = [];

    /**
     *
     * @throws CurlException
     * @see http://www.php.net/manual/en/function.curl-init.php
     */
    public function __construct()
    {
        $curlSession = curl_init();
        if ($curlSession === false) {
            throw new CurlException('cURL session could not be initialized');
        }
        $this->curlSession = $curlSession;
    }

    public function getCurlSession(): \CurlHandle
    {
        return $this->curlSession;
    }

    /**
     *
     * @throws CurlException
     * @see http://www.php.net/manual/en/function.curl-copy-handle.php
     */
    public function __clone()
    {
        $curlSession = curl_copy_handle($this->curlSession);
        if ($curlSession === false) {
            throw new CurlException('cURL session could not be copied');
        }
        $this->curlSession = $curlSession;
        // TODO: does $options need to be copied too?
    }

    /**
     *
     * @return self
     * @throws CurlException
     */
    public function autoDetectUserAgent(): self
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? USER_AGENT_CHROME;
        $this->setOption(CURLOPT_USERAGENT, $agent);
        return $this;
    }

    /**
     *
     * @param string $string
     *            The string to be encoded.
     * @throws CurlException
     * @see http://www.php.net/manual/en/function.curl-escape.php
     */
    public function escape($string): string
    {
        $result = curl_escape($this->curlSession, $string);
        if ($result === false) {
            throw new CurlException(__METHOD__ . "($string)");
        }
        return $result;
    }

    /**
     *
     * @param string $string
     *            The URL encoded string to be decoded.
     * @throws CurlException
     * @see http://www.php.net/manual/en/function.curl-unescape.php
     */
    public function unescape($string): string
    {
        $result = curl_unescape($this->curlSession, $string);
        if ($result === false) {
            throw new CurlException(__METHOD__ . "($string)");
        }
        return $result;
    }

    /**
     * Cause this curl session to use a temporary cookie file which is
     * automatically deleted when the script ends.
     *
     * @return self
     * @throws CurlException
     */
    public function autoCookieJar($cookiePath = null)
    {
        if ($cookiePath === null) {
            $cookiePath = File::createTempFile('COOKIE')->getRealPath();
        }

        $this->setOptions([
            CURLOPT_COOKIEJAR => $cookiePath,
            CURLOPT_COOKIEFILE => $cookiePath,
        ]);
        return $this;
    }

    /**
     *
     * @param mixed $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getOption($name);
    }

    /**
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $magicOption = $this->magicOption($name);
        $this->setOption($magicOption, $value);
    }

    /**
     *
     * @param string $name
     * @param array $arguments
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments)
    {
        $nameStartsWithSet = strpos($name, $needle = 'set') === 0;
        $count = count($arguments);

        switch ($count) {
            case 0:
                $value = true;
                break;
            case 1:
                $value = array_shift($arguments);
                break;
        }

        if ($nameStartsWithSet && isset($value)) {
            $setter = substr($name, $start = strlen($needle));

            // https://stackoverflow.com/a/19533226/625688
            $underscore = preg_replace('/(?<!^)[A-Z]/', '_$0', $setter);
            $upper = strtoupper($underscore);
            $this->$upper = $value;
            return $this;
        }

        $message = "$name(...)";
        throw new \BadMethodCallException($message);
    }

    /**
     *
     * @param mixed $option
     * @return bool
     */
    public function __isset($option)
    {
        $magicOption = $this->magicOption($option);
        return isset($this->options[$magicOption]);
    }

    /**
     *
     * @param mixed $option
     * @throws \RuntimeException
     */
    public function __unset($option)
    {
        $message = "unsupported operation: __unset($option)";
        throw new \RuntimeException($message);
    }

    /**
     *
     * @param mixed $option
     * @return mixed
     */
    public function getOption($option)
    {
        $magicOption = $this->magicOption($option);
        return $this->options[$magicOption];
    }

    /**
     *
     * @param mixed $option
     * @throws \InvalidArgumentException
     * @return mixed
     */
    protected function magicOption($option)
    {
        // setOption() is called with a CURLOPT_* value, so options are keyed by int
        if (is_int($option)) {
            return $option;
        }

        if (defined($option)) {
            return constant($option);
        }

        $prefixes = [
            'CURLOPT_',
            'CURLINFO_',
            'CURLMOPT_',
            'CURLSSH_',
            'CURLSSLOPT_',
            'CURL_',
        ];
        foreach ($prefixes as $prefix) {
            $curlopt = $prefix . strtoupper($option);
            if (defined($curlopt)) {
                return constant($curlopt);
            }
        }

        throw new \InvalidArgumentException($option);
    }

    /**
     *
     * @param mixed $option
     * @param mixed $value
     * @throws CurlException
     * @return self
     * @see http://php.net/curl_setopt
     */
    public function setOption($option, $value): self
    {
        $this->options[$option] = $value;
        $result = curl_setopt($this->curlSession, $option, $value);
        if ($result === false) {
            throw new CurlException("option $option could not be set");
        }
        return $this;
    }

    /**
     *
     * @param array $options
     * @return self
     */
    public function setOptions(array $options): self
    {
        // curl_setopt_array($this->session, $options);
        // this way we get a useful message in the event of an exception
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
        return $this;
    }

    /**
     *
     * @param string $url
     * @return \tagadvance\stooge\CurlResponse
     */
    public function get(string $url): CurlResponse
    {
        return $this->setUrl($url)->execute();
    }

    /**
     *
     * @param mixed $fields
     * @return CurlResponse
     */
    public function post($url, $fields): CurlResponse
    {
        return $this->setUrl($url)
            ->setOptions([
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $fields,
            ])
            ->execute();
    }

    /**
     *
     * @param mixed $fields
     * @return CurlResponse
     */
    public function put($url, $fields): CurlResponse
    {
        return $this->setUrl($url)
            ->setOptions([
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $fields,
            ])
            ->execute();
    }

    /**
     *
     * @param mixed $fields
     * @return CurlResponse
     */
    public function patch($url, $fields): CurlResponse
    {
        return $this->setUrl($url)
            ->setOptions([
                CURLOPT_CUSTOMREQUEST => 'PATCH',
                CURLOPT_POSTFIELDS => $fields,
            ])
            ->execute();
    }

    /**
     *
     * @param mixed $fields
     * @return CurlResponse
     */
    public function delete($url, $fields): CurlResponse
    {
        return $this->setUrl($url)
            ->setOptions([
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_POSTFIELDS => $fields,
            ])
            ->execute();
    }

    /**
     *
     * @return \tagadvance\stooge\CurlResponse
     */
    public function __invoke()
    {
        return $this->execute();
    }

    /**
     * TODO: add proxy support
     * http://stackoverflow.com/questions/9183178/php-curl-retrieving-response-headers-and-body-in-a-single-request
     *
     * @return CurlResponse
     */
    public function execute(): CurlResponse
    {
        $callback = $this->HEADERFUNCTION ?? function ($curlResource, $headerData) {
            return strlen($headerData);
        };
        $headerText = '';
        $this->HEADERFUNCTION = function ($curlResource, $headerData) use ($callback, &$headerText) {
            $headerText .= $headerData;
            return $callback($curlResource, $headerData);
        };

        $result = $this->rawExec();

        $parser = new MKJHeaderParser();
        $headers = $parser->parseHeaders($headerText);

        if (isset($this->CURLOPT_HEADER) && $this->CURLOPT_HEADER) {
            $headerSize = curl_getinfo($this->curlSession, CURLINFO_HEADER_SIZE);
            $result = substr($result, $headerSize);
        }

        $code = $this->getInformation(CURLINFO_HTTP_CODE);
        return new CurlResponse($code, $headers, $result);
    }

    /**
     * Return raw, unprocessed result.
     *
     * @throws CurlException
     * @return mixed
     */
    public function rawExec()
    {
        $result = curl_exec($this->curlSession);
        if ($result === false) {
            $message = curl_error($this->curlSession);
            $code = curl_errno($this->curlSession);
            throw new CurlException($message, $code);
        }
        return $result;
    }

    /**
     *
     * @return self
     * @see http://php.net/manual/en/function.curl-reset.php
     */
    public function reset(): self
    {
        curl_reset($this->curlSession);
        return $this;
    }

    /**
     *
     * @return self
     * @see http://php.net/manual/en/function.curl-close.php
     */
    public function close(): self
    {
        curl_close($this->curlSession);
        return $this;
    }

    /**
     *
     * @param integer $option
     * @see http://www.php.net/manual/en/function.curl-getinfo.php
     */
    public function getInformation($option = null)
    {
        return curl_getinfo($this->curlSession, $option);
    }

    /**
     *
     * @return array
     * @see http://www.php.net/manual/en/function.curl-version.php
     */
    public static function version(): array
    {
        return curl_version();
    }

}
