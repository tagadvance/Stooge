<?php

namespace tagadvance\stooge;

class URLQuery
{
    private $parameters;

    public static function createFromQueryString(string $query, $prefix = '?', $keyValuePairSeparator = '=', $delimiter = '&'): self
    {
        $parameters = [];

        if (strpos($query, $prefix) === 0) {
            $query = substr($query, $start = strlen($prefix));
        }

        $keyValuePairs = explode($delimiter, $query);
        foreach ($keyValuePairs as $pair) {
            list($key, $value) = explode($keyValuePairSeparator, $pair);
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

    public function __get(string $name)
    {
        return $this->parameters[$name];
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
