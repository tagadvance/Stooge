<?php

namespace tagadvance\stooge;

/**
 * A mutable bag of query parameters exposed as properties. Values are held
 * decoded and are only percent-encoded by {@see URLQuery::toString()}.
 */
class URLQuery
{
    private $parameters;

    /**
     * A repeated key collapses to its last occurrence, and PHP's `a[]=1&a[]=2`
     * convention is not expanded — the literal key `a[]` is stored instead.
     *
     * @param string $prefix
     *            Stripped from the front of $query when present.
     */
    public static function createFromQueryString(string $query, $prefix = '?', $keyValuePairSeparator = '=', $delimiter = '&'): self
    {
        $parameters = [];

        if (strpos($query, $prefix) === 0) {
            $query = substr($query, $start = strlen($prefix));
        }

        $keyValuePairs = explode($delimiter, $query);
        foreach ($keyValuePairs as $pair) {
            if ($pair === '') {
                continue;
            }

            // split on the first separator only; a value may contain more
            list($key, $value) = array_pad(explode($keyValuePairSeparator, $pair, 2), 2, '');
            $decodedKey = urldecode($key);
            $decodedValue = urldecode($value);
            $parameters[$decodedKey] = $decodedValue;
        }

        return new self($parameters);
    }

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * An absent parameter reads as null.
     */
    public function __get(string $name)
    {
        return $this->parameters[$name] ?? null;
    }

    public function __set(string $name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function __isset(string $name)
    {
        return isset($this->parameters[$name]);
    }

    public function __unset(string $name)
    {
        unset($this->parameters[$name]);
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Encoding follows `application/x-www-form-urlencoded` rather than RFC 3986,
     * so a space becomes `+` and not `%20`.
     */
    public function toString($prefix = '?', $keyValuePairSeparator = '=', $delimiter = '&'): string
    {
        $string = $prefix;
        foreach ($this->parameters as $key => $value) {
            if (strlen($string) > strlen($prefix)) {
                $string .= $delimiter;
            }
            $encodedKey = urlencode($key);
            $encodedValue = urlencode($value);
            $string .= "$encodedKey$keyValuePairSeparator$encodedValue";
        }
        return $string;
    }

    public function __sleep()
    {
        return [
            'parameters',
        ];
    }

}
