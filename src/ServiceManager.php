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

use Huangdijia\Jet\Contract\DataFormatterInterface;
use Huangdijia\Jet\Contract\PackerInterface;
use Huangdijia\Jet\Contract\PathGeneratorInterface;
use Huangdijia\Jet\Contract\RegistryInterface;
use Huangdijia\Jet\Contract\TransporterInterface;
use Huangdijia\Jet\Exception\JetException;
use InvalidArgumentException;

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

    /**
     * @throws InvalidArgumentException
     */
    public static function register(string $service, array $metadata = [])
    {
        static::assertTransporter($metadata[static::TRANSPORTER] ?? null);
        static::assertRegistry($metadata[static::REGISTRY] ?? null);
        static::assertPacker($metadata[static::PACKER] ?? null);
        static::assertDataFormatter($metadata[static::DATA_FORMATTER] ?? null);
        static::assertPathGenerator($metadata[static::PATH_GENERATOR] ?? null);
        static::assertTries($metadata[static::TRIES] ?? null);

        static::$services[$service] = $metadata;
    }

    public static function deregister(string $service)
    {
        unset(static::$services[$service]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws JetException
     */
    public static function registerDefaultRegistry(RegistryInterface $registry)
    {
        RegistryManager::register(RegistryManager::DEFAULT, $registry);
    }

    /**
     * @return null|RegistryInterface
     */
    public static function getDefaultRegistry()
    {
        return RegistryManager::get(RegistryManager::DEFAULT);
    }

    /**
     * @param mixed $transporter
     * @throws InvalidArgumentException
     */
    public static function assertTransporter($transporter)
    {
        if (! is_null($transporter) && ! ($transporter instanceof TransporterInterface)) {
            throw new InvalidArgumentException(sprintf('Transporter of service must be instanceof %s.', TransporterInterface::class));
        }
    }

    /**
     * @param mixed $registry
     * @throws InvalidArgumentException
     */
    public static function assertRegistry($registry)
    {
        if (is_null($registry)) {
            return;
        }

        if (is_string($registry)) {
            if (! RegistryManager::isRegistered($registry)) {
                throw new InvalidArgumentException(sprintf('Registry %s does not registered yet.', $registry));
            }
        } elseif (! ($registry instanceof RegistryInterface)) {
            throw new InvalidArgumentException(sprintf('Register of service must be instanceof %s.', RegistryInterface::class));
        }
    }

    /**
     * @param mixed $packer
     * @throws InvalidArgumentException
     */
    public static function assertPacker($packer)
    {
        if (! is_null($packer) && ! ($packer instanceof PackerInterface)) {
            throw new InvalidArgumentException(sprintf('Packer of service must be instanceof %s.', PackerInterface::class));
        }
    }

    /**
     * @param mixed $dataFormatter
     * @throws InvalidArgumentException
     */
    public static function assertDataFormatter($dataFormatter)
    {
        if (! is_null($dataFormatter) && ! ($dataFormatter instanceof DataFormatterInterface)) {
            throw new InvalidArgumentException(sprintf('Service\'s DATA_FORMATTER must be instanceof %s.', DataFormatterInterface::class));
        }
    }

    /**
     * @param mixed $pathGenerator
     * @throws InvalidArgumentException
     */
    public static function assertPathGenerator($pathGenerator)
    {
        if (! is_null($pathGenerator) && ! ($pathGenerator instanceof PathGeneratorInterface)) {
            throw new InvalidArgumentException(sprintf('PathGenerator of service must be instanceof %s.', PathGeneratorInterface::class));
        }
    }

    /**
     * @param mixed $tries
     * @throws InvalidArgumentException
     */
    public static function assertTries($tries)
    {
        if (! is_null($tries) && ! is_int($tries)) {
            throw new InvalidArgumentException('Tries of service must be int.');
        }
    }
}
