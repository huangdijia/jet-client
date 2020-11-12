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

use Exception;
use Huangdijia\Jet\Contract\DataFormatterInterface;
use Huangdijia\Jet\Contract\PackerInterface;
use Huangdijia\Jet\Contract\PathGeneratorInterface;
use Huangdijia\Jet\Contract\RegistryInterface;
use Huangdijia\Jet\Contract\TransporterInterface;
use Huangdijia\Jet\Exception\ClientException;
use InvalidArgumentException;

class ClientFactory
{
    /**
     * Create a client.
     *
     * @param null|string|TransporterInterface $transporter transporter, protocol or null
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function create(string $service, $transporter = null, ?PackerInterface $packer = null, ?DataFormatterInterface $dataFormatter = null, ?PathGeneratorInterface $pathGenerator = null, ?int $tries = null): Client
    {
        $protocol = null;

        if ($transporter instanceof TransporterInterface) {
            return static::createWithTransporter(...func_get_args());
        }

        // when $transporter is string
        if (is_string($transporter)) {
            $protocol = $transporter;
            $transporter = null;
        }

        $serviceMetadata = ServiceManager::get($service);

        if (! $serviceMetadata) {
            if ($registry = ServiceManager::getDefaultRegistry()) {
                return self::createWithRegistry($service, $registry, $protocol, $packer, $dataFormatter, $pathGenerator, $tries);
            }

            throw new ClientException(sprintf('Service %s does not register yet.', $service));
        }

        if (isset($serviceMetadata[ServiceManager::TRANSPORTER])) { // preference to using transporter
            /** @var TransporterInterface $transporter */
            $transporter = $serviceMetadata[ServiceManager::TRANSPORTER];
        } elseif (isset($serviceMetadata[ServiceManager::REGISTRY])) { // using registry
            /** @var RegistryInterface $registry */
            $registry = $serviceMetadata[ServiceManager::REGISTRY];
            $transporter = $registry->getTransporter($service, $protocol);
        }

        if (! $transporter) {
            throw new ClientException(sprintf('Service %s\'s transporter does not register yet.', $service));
        }

        $packer = $packer ?? $serviceMetadata[ServiceManager::PACKER] ?? null;
        $dataFormatter = $dataFormatter ?? $serviceMetadata[ServiceManager::DATA_FORMATTER] ?? null;
        $pathGenerator = $pathGenerator ?? $serviceMetadata[ServiceManager::PATH_GENERATOR] ?? null;
        $tries = $tries ?? $serviceMetadata[ServiceManager::TRIES] ?? 1;

        return static::createWithTransporter($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }

    /**
     * Create a client with transporter.
     */
    public static function createWithTransporter(string $service, TransporterInterface $transporter, ?PackerInterface $packer = null, ?DataFormatterInterface $dataFormatter = null, ?PathGeneratorInterface $pathGenerator = null, ?int $tries = null): Client
    {
        return new Client($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }

    public static function createWithRegistry(string $service, RegistryInterface $registry, ?string $protocol = null, ?PackerInterface $packer = null, ?DataFormatterInterface $dataFormatter = null, ?PathGeneratorInterface $pathGenerator = null, ?int $tries = null): Client
    {
        $transporter = $registry->getTransporter($service, $protocol);

        return new Client($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }
}