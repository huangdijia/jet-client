<?php

abstract class JetFacade
{
    /**
     * @return JetClient|string
     * @throws RuntimeException
     */
    public static function getFacadeAccessor()
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
        $accessor = static::getFacadeAccessor();

        if (is_string($accessor)) {
            $accessor = JetClientFactory::create($accessor);
        }

        return call_user_func_array(array($accessor, $name), $arguments);
    }
}
