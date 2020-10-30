<?php

class JetUtil
{
    /**
     * Retry
     * @param int $times
     * @param Closure $callback
     * @param int $sleep
     * @return mixed
     */
    public static function retry($times, $callback, $sleep = 0)
    {
        beginning:

        try {
            return $callback();
        } catch (Exception $e) {
            if (--$times < 0) {
                throw $e;
            }

            sleep($sleep);

            goto beginning;
        }
    }

    /**
     * @param mixed $value
     * @param Exception $exception
     * @return mixed
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function throwIf($value, $exception)
    {
        if (!($exception instanceof Exception)) {
            throw new InvalidArgumentException('$exception is not instanceof Exception');
        }

        if ($value) {
            throw $exception;
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param Closure|null $callback
     * @return mixed
     */
    public static function tap($value, $callback = null)
    {
        if (!is_null($callback) && is_callable($callback)) {
            $callback($value);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param Closure|null $callback
     * @return mixed
     */
    public static function with($value, $callback)
    {
        if (!is_null($callback) && is_callable($callback)) {
            return $callback($value);
        }

        return $value;
    }

    /**
     * @param string $value 
     * @param string $delimiter 
     * @return mixed 
     */
    public static function snake($value, $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return $value;
    }

    /**
     * @param string $value 
     * @return string 
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * @param mixed $value 
     * @return mixed 
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
