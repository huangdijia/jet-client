<?php

namespace Huangdijia\Jet;

class ServiceManager
{
    const REGISTRY       = 'rg';
    const TRANSPORTER    = 'tp';
    const PACKER         = 'pk';
    const DATA_FORMATTER = 'df';
    const PATH_GENERATOR = 'pg';
    const TRIES          = 'ts';

    /**
     * @var array
     */
    protected static $services = [];

    /**
     * @param string $service
     * @return array
     */
    public static function get(string $service)
    {
        return self::isRegistered($service) ? static::$services[$service] : [];
    }

    /**
     * @param string $service
     * @return bool
     */
    public static function isRegistered(string $service)
    {
        return isset(static::$services[$service]);
    }

    /**
     * 
     * @param string $service 
     * @param array $metadata 
     * @return void 
     */
    public static function register(string $service, array $metadata = [])
    {
        static::$services[$service] = $metadata;
    }

    /**
     * @param string $service
     * @return void
     */
    public static function deregister(string $service)
    {
        unset(static::$services[$service]);
    }
}
