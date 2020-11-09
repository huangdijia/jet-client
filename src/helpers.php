<?php
if (!function_exists('retry')) {
    /**
     * Retry
     * @param int $times
     * @param callable $callback
     * @param int $sleep
     * @param  callable|null  $when
     * @return mixed
     */
    function retry(int $times, callable $callback, int $sleep = 0, callable $when = null)
    {
        beginning:

        try {
            return $callback();
        } catch (\Throwable $e) {
            if ($times < 1 || ($when && !$when($e))) {
                throw $e;
            }

            if ($sleep) {
                sleep($sleep);
            }

            goto beginning;
        }
    }
}

if (!function_exists('throw_if')) {
    /**
     * @param mixed $condition
     * @param \Throwable|string $exception
     * @return mixed
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function throw_if($condition, $exception, ...$parameters)
    {
        if ($condition) {
            throw (is_string($exception) ? new $exception(...$parameters) : $exception);
        }

        return $condition;
    }
}

if (!function_exists('tap')) {
    /**
     * @param mixed $value
     * @param callable|null $callback
     * @return mixed
     */
    function tap($value, ?callable $callback = null)
    {
        if (!is_null($callback)) {
            return new class($value)
            {
                public $target;

                public function __construct($target)
                {
                    $this->target = $target;
                }

                public function __call($method, $parameters)
                {
                    $this->target->{$method}(...$parameters);

                    return $this->target;
                }
            };
        }

        $callback($value);

        return $value;
    }
}

if (!function_exists('with')) {
    /**
     * @param mixed $value
     * @param callable|null $callback
     * @return mixed
     */
    function with($value, callable $callback = null)
    {
        return is_null($callback) ? $value : $callback($value);
    }
}

if (!function_exists('str_snake')) {
    /**
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    function str_snake($value, $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = str_lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return $value;
    }
}

if (!function_exists('str_lower')) {
    /**
     * @param string $value
     * @return string
     */
    function str_lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }
}

if (!function_exists('value')) {
    /**
     * @param mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }

}

if (!function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array|\ArrayAccess $array
     * @param null|int|string $key
     * @param mixed $default
     */
    function array_get($array, $key = null, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        if (!is_string($key) || strpos($key, '.') === false) {
            return $array[$key] ?? value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (array_accessible($array) && array_exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }
}

if (!function_exists('array_has')) {
    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param array|\ArrayAccess $array
     * @param null|array|string $keys
     */
    function array_has($array, $keys)
    {
        if (is_null($keys)) {
            return false;
        }

        $keys = (array) $keys;

        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (array_exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (array_accessible($subKeyArray) && array_exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}

if (!function_exists('array_exists')) {
    /**
     *
     * @param mixed $array
     * @param int|string $key
     * @return bool
     */
    function array_exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }
}

if (!function_exists('array_accessible')) {
    /**
     * @param mixed $value
     * @return bool
     */
    function array_accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
}
