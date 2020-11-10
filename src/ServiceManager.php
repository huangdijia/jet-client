<?php

declare(strict_types=1);
/**
 * This file is part of Jet-Client.
 *
 * @link     https://github.com/huangdijia/jet-client
 * @document https://github.com/huangdijia/jet-client/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/jet-client/blob/main/LICENSE
 */
namespace Huangdijia\Jet;

class ServiceManager
{
    const REGISTRY = 'rg';

    const TRANSPORTER = 'tp';

    const PACKER = 'pk';

    const DATA_FORMATTER = 'df';

    const PATH_GENERATOR = 'pg';

    const TRIES = 'ts';

    /**
     * @var array
     */
    protected static $services = [];

    /**
     * @return array
     */
    public static function get(string $service)
    {
        return self::isRegistered($service) ? static::$services[$service] : [];
    }

    /**
     * @return bool
     */
    public static function isRegistered(string $service)
    {
        return isset(static::$services[$service]);
    }

    public static function register(string $service, array $metadata = [])
    {
        static::$services[$service] = $metadata;
    }

    public static function deregister(string $service)
    {
        unset(static::$services[$service]);
    }
}
