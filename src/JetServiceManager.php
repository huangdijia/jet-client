<?php

class JetServiceManager
{
    const NODES = 'nodes';

    /**
     * @var array
     */
    protected static $services = array();

    /**
     * @param mixed $service
     * @param mixed $protocol
     * @return array
     */
    public static function get($service, $protocol)
    {
        return self::isRegistered($service, $protocol) ? static::$services[static::buildKey($service, $protocol)] : array();
    }

    /**
     * @param string $service
     * @param string $protocol
     * @return bool
     */
    public static function isRegistered($service, $protocol)
    {
        return isset(static::$services[static::buildKey($service, $protocol)]);
    }

    /**
     * @param string $service
     * @param string $protocol
     * @param array $metadata
     * @return void
     * @throws Exception
     */
    public static function register($service, $protocol, $metadata = array())
    {
        if (!JetProtocolManager::isRegistered($protocol)) {
            throw new Exception(sprintf('The protocol %s does not register to %s yet.', JetProtocolManager::class, $protocol));
        }

        static::$services[static::buildKey($service, $protocol)] = $metadata;
    }

    /**
     * @param string $service
     * @param string $protocol
     * @return void
     */
    public static function deregister($service, $protocol)
    {
        unset(static::$services[static::buildKey($service, $protocol)]);
    }

    /**
     * @param string $service
     * @param string $protocol
     * @return string
     */
    private static function buildKey($service, $protocol)
    {
        return $service . '@' . $protocol;
    }
}
