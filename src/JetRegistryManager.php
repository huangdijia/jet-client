<?php

class JetRegistryManager
{
    const DEFAULT_REGISTRY = 'default';

    /**
     * @var array
     */
    protected static $registries = array();

    /**
     * @param string $name 
     * @return JetRegistryInterface|null 
     */
    public static function get($name)
    {
        return isset(self::$registries[$name]) ? self::$registries[$name] : null;
    }

    /**
     * @param string $name 
     * @param JetRegistryInterface $registry 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    public static function register($name, $registry)
    {
        if (!($registry instanceof JetRegistryInterface)) {
            throw new InvalidArgumentException('$registry must be instanceof JetRegistryInterface');
        }

        if (self::isRegistered($name)) {
            throw new RuntimeException($name . ' has registered');
        }

        self::$registries[$name] = $registry;
    }

    /**
     * @param string $name 
     * @return bool 
     */
    public static function isRegistered($name)
    {
        return isset(self::$registries[$name]);
    }

    /**
     * @param string $name 
     * @return void 
     */
    public static function deregister($name)
    {
        unset(static::$registries[$name]);
    }
}
