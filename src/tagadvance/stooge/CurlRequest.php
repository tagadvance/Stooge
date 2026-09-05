<?php

namespace tagadvance\stooge;

/**
 * Declared at file scope, so autoloading this class defines a *global* constant
 * as a side effect. The value is a Chrome 58 string frozen in 2017.
 */
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
     * Local mirror of every option set through this object, keyed by `CURLOPT_*`
     * value. libcurl cannot be read back, so this is what `getOption()` answers
     * from.
     *
     * @var array
     */
    private $options = [];

    /**
     * @throws CurlException when a cURL session could not be initialized.
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
     * @throws CurlException when libcurl could not duplicate the handle.
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
     * The longest inbound User-Agent that will be forwarded. Real ones are well under
     * 300 bytes; anything longer is not a browser.
     */
    private const MAX_USER_AGENT_LENGTH = 1024;

    /**
     * Forwards the inbound `$_SERVER['HTTP_USER_AGENT']` on this outbound request, falling
     * back to `USER_AGENT_CHROME` when there is none or when it is not safe to forward.
     *
     * The inbound header is attacker controlled, and libcurl copies a CR or LF in it
     * straight onto the wire, which appends headers of the attacker's choosing to every
     * request this object makes.
     *
     * @throws CurlException when the option could not be set.
     */
    public function autoDetectUserAgent(): self
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        if (! is_string($agent) || ! self::isForwardableUserAgent($agent)) {
            $agent = USER_AGENT_CHROME;
        }

        $this->setOption(CURLOPT_USERAGENT, $agent);
        return $this;
    }

    /**
     * Rejects control characters, which is where header injection lives, and anything
     * longer than MAX_USER_AGENT_LENGTH.
     */
    private static function isForwardableUserAgent(string $agent): bool
    {
        return $agent !== ''
            && strlen($agent) <= self::MAX_USER_AGENT_LENGTH
            && preg_match('/[\x00-\x1F\x7F]/', $agent) !== 1;
    }

    /**
     * Percent-encodes by libcurl's rules rather than PHP's, so a space becomes
     * `%20` and not `+`.
     *
     * @param string $string
     * @throws CurlException when libcurl rejects the input.
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
     * The inverse of {@see CurlRequest::escape()}, so a `+` is left as-is instead
     * of being decoded to a space.
     *
     * @param string $string
     * @throws CurlException when libcurl rejects the input.
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
     * Give this session a cookie jar so a server session survives across requests.
     * With no argument the jar is a temporary file deleted when the script ends;
     * pass a path to keep the cookies beyond it.
     *
     * @param ?string $cookiePath
     * @return self
     * @throws CurlException when the temporary file or the options could not be set.
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
     * @see CurlRequest::getOption()
     */
    public function __get($name)
    {
        return $this->getOption($name);
    }

    public function __set($name, $value)
    {
        $magicOption = $this->magicOption($name);
        $this->setOption($magicOption, $value);
    }

    /**
     * A zero-argument `setFoo()` sets `CURLOPT_FOO` to `true`, while a single
     * explicit `null` is rejected rather than passed through. `setUserAgent()`
     * resolves to the undefined `CURLOPT_USER_AGENT` and throws — reach that
     * option as `setUseragent()` or `setOption(CURLOPT_USERAGENT, ...)`.
     *
     * @throws \BadMethodCallException when $name is not a `set*` method, or more
     *         than one argument was passed.
     * @throws \InvalidArgumentException when no cURL option matches $name.
     * @throws CurlException when libcurl rejects the option or its value.
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
     * Reports whether the option was set through this object, not whether libcurl
     * holds a value for it.
     *
     * @throws \InvalidArgumentException when no cURL option matches $option.
     */
    public function __isset($option)
    {
        $magicOption = $this->magicOption($option);
        return isset($this->options[$magicOption]);
    }

    /**
     * @throws \RuntimeException always; libcurl offers no way to unset an option.
     */
    public function __unset($option)
    {
        $message = "unsupported operation: __unset($option)";
        throw new \RuntimeException($message);
    }

    /**
     * Answers from the local mirror, so an option libcurl holds but this object
     * never set counts as absent. An absent option emits an "Undefined array key"
     * warning and returns null.
     *
     * @throws \InvalidArgumentException when no cURL option matches $option.
     */
    public function getOption($option)
    {
        $magicOption = $this->magicOption($option);
        return $this->options[$magicOption];
    }

    /**
     * Resolves a cURL option to its integer value, accepting an int, a full
     * constant name, or a bare suffix such as `RETURNTRANSFER`.
     *
     * @throws \InvalidArgumentException when no cURL constant matches.
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
     * Unlike the magic setters, this wants the `CURLOPT_*` constant itself; an
     * option name is not resolved here.
     *
     * @throws CurlException when libcurl rejects the option or its value.
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
     * Options are applied one at a time so that a failure names the offending
     * option, which does mean a failure part-way leaves the earlier ones set.
     *
     * @param array $options
     *            Keyed by `CURLOPT_*` constant.
     * @throws CurlException when libcurl rejects an option or its value.
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
     * Options from an earlier request are still in force, so a `get()` after a
     * `post()` on the same object still sends POST; call {@see CurlRequest::reset()}
     * in between.
     *
     * @throws CurlException when the request fails.
     */
    public function get(string $url): CurlResponse
    {
        return $this->setUrl($url)->execute();
    }

    /**
     * @param mixed $fields
     *            An array, which is sent as `multipart/form-data`, or a
     *            `key=value&...` string, which is sent as
     *            `application/x-www-form-urlencoded`.
     * @throws CurlException when the request fails.
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
     * @param mixed $fields
     *            An array, which is sent as `multipart/form-data`, or a
     *            `key=value&...` string, which is sent as
     *            `application/x-www-form-urlencoded`.
     * @throws CurlException when the request fails.
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
     * @param mixed $fields
     *            An array, which is sent as `multipart/form-data`, or a
     *            `key=value&...` string, which is sent as
     *            `application/x-www-form-urlencoded`.
     * @throws CurlException when the request fails.
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
     * @param mixed $fields
     *            An array, which is sent as `multipart/form-data`, or a
     *            `key=value&...` string, which is sent as
     *            `application/x-www-form-urlencoded`.
     * @throws CurlException when the request fails.
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
     * Alias for {@see CurlRequest::execute()}, so a prepared request can be passed
     * around as a callable.
     *
     * @return \tagadvance\stooge\CurlResponse
     */
    public function __invoke()
    {
        return $this->execute();
    }

    /**
     * Runs the request, capturing the headers of every hop along the way.
     * `CURLOPT_RETURNTRANSFER` is not forced, so unless the caller set it libcurl
     * writes the body to output and the response body is the string "1".
     *
     * @throws CurlException when the request fails.
     * @todo add proxy support
     * @see http://stackoverflow.com/questions/9183178/php-curl-retrieving-response-headers-and-body-in-a-single-request
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
     * The unprocessed `curl_exec()` result, without the header capture and
     * header/body split {@see CurlRequest::execute()} performs. Returns the body
     * when `CURLOPT_RETURNTRANSFER` is set, and otherwise `true`, having written
     * the body to output.
     *
     * @throws CurlException when libcurl reports an error.
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
     * Restores libcurl's defaults. The local option mirror is not cleared, so
     * `getOption()` keeps answering with pre-reset values.
     *
     * @see http://php.net/manual/en/function.curl-reset.php
     */
    public function reset(): self
    {
        curl_reset($this->curlSession);
        return $this;
    }

    /**
     * Since PHP 8.0 the handle is an object released on garbage collection, so
     * this has no lasting effect and the request stays usable afterwards.
     *
     * @see http://php.net/manual/en/function.curl-close.php
     */
    public function close(): self
    {
        curl_close($this->curlSession);
        return $this;
    }

    /**
     * Called with no argument this returns the whole information array rather
     * than a single value.
     *
     * @param ?integer $option
     *            A `CURLINFO_*` constant.
     * @see http://www.php.net/manual/en/function.curl-getinfo.php
     */
    public function getInformation($option = null)
    {
        return curl_getinfo($this->curlSession, $option);
    }

    /**
     * @see http://www.php.net/manual/en/function.curl-version.php
     */
    public static function version(): array
    {
        return curl_version();
    }

}
