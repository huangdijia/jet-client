<?php

class JetServiceManager
{
    const REGISTRY       = 'rg';
    const TRANSPORTER    = 'tp';
    const PACKER         = 'pk';
    const DATA_FORMATTER = 'df';
    const PATH_GENERATOR = 'pg';

    /**
     * @var array
     */
    protected static $services = array();

    /**
     * @param string $service
     * @return array
     */
    public static function get($service)
    {
        return self::isRegistered($service) ? static::$services[$service] : array();
    }

    /**
     * @param string $service
     * @return bool
     */
    public static function isRegistered($service)
    {
        return isset(static::$services[$service]);
    }

    /**
     * @param string $service
     * @param array $metadata
     * @return void
     * @throws Exception
     */
    public static function register($service, $metadata = array())
    {
        static::$services[$service] = $metadata;
    }

    /**
     * @param string $service
     * @param string $protocol
     * @return void
     */
    public static function deregister($service)
    {
        unset(static::$services[$service]);
    }
}
