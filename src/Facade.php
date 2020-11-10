<?php

namespace Huangdijia\Jet;

use Exception;
use RuntimeException;
use Huangdijia\Jet\ClientFactory;
use InvalidArgumentException;

abstract class Facade
{
    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * 
     * @return Client 
     * @throws RuntimeException 
     */
    protected static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * 
     * @param mixed $name 
     * @return mixed 
     * @throws InvalidArgumentException 
     * @throws Exception 
     */
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }

        return static::$instances[$name] = ClientFactory::create($name);
    }

    /**
     * @return Client|string
     * @throws RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        // return call_user_func_array([static::getFacadeRoot(), $name], $arguments);
        return static::getFacadeRoot()->$name(...$arguments);
    }
}
