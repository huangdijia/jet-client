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

    /**
     * @param string $service 
     * @param string $method 
     * @return string 
     */
    public static function generatePath($service, $method)
    {
        $handledNamespace = explode('\\', $service);
        $handledNamespace = str_replace('\\', '/', end($handledNamespace));
        $handledNamespace = str_replace('Service', '', $handledNamespace);
        $path             = self::snake($handledNamespace);

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        return $path . '/' . $method;
    }

    /**
     * @param array $data 
     * @return array 
     */
    public static function formatRequest($data = array())
    {
        $path   = isset($data[0]) ? $data[0] : '';
        $params = isset($data[1]) ? $data[1] : array();
        $id     = isset($data[2]) ? $data[2] : uniqid();

        return array(
            'jsonrpc' => '2.0',
            'method'  => $path,
            'params'  => $params,
            'id'      => $id,
            'data'    => array(),
        );
    }

    /**
     * @param mixed $data 
     * @param string $eof 
     * @return string 
     */
    public static function jsonEofPack($data, $eof = "\r\n")
    {
        $data = json_encode($data);

        return $data . $eof;
    }

    /**
     * @param string $data 
     * @return array 
     */
    public static function jsonEofUnpack($data)
    {
        return json_decode($data, true);
    }
}
