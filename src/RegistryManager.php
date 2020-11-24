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

use Huangdijia\Jet\Contract\RegistryInterface;
use Huangdijia\Jet\Exception\JetException;
use InvalidArgumentException;

class RegistryManager
{
    const DEFAULT = 'default';

    /**
     * @var array
     */
    protected static $registries = [];

    /**
     * @param string $name
     * @return null|RegistryInterface
     */
    public static function get($name = self::DEFAULT)
    {
        return isset(self::$registries[$name]) ? self::$registries[$name] : null;
    }

    /**
     * @param string $name
     * @param RegistryInterface $registry
     * @throws InvalidArgumentException
     * @throws JetException
     */
    public static function register($name, $registry)
    {
        if (! ($registry instanceof RegistryInterface)) {
            throw new InvalidArgumentException('$registry must be instanceof RegistryInterface');
        }

        if (self::isRegistered($name)) {
            throw new JetException($name . ' has registered');
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
     */
    public static function deregister($name)
    {
        unset(static::$registries[$name]);
    }
}
