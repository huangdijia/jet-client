<?php

abstract class JetFacade
{
    /**
     * @var array
     */
    protected static $instances = array();

    /**
     * @return JetClient
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * @param JetClient|string $name
     * @return JetClient
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

        return static::$instances[$name] = JetClientFactory::create($name);
    }

    /**
     * @return JetClient|string
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
        return call_user_func_array(array(static::getFacadeRoot(), $name), $arguments);
    }
}
