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
use Huangdijia\Jet\Exception\ClientException;

class ClientFactory
{
    /**
     * Create a client.
     * @param null|string|TransporterInterface $transporter
     * @return Client
     */
    public static function create(string $service, $transporter = null)
    {
        $packer = null;
        $dataFormatter = null;
        $pathGenerator = null;
        $protocol = null;
        $tries = 1;

        if (! ($transporter instanceof TransporterInterface)) {
            $serviceMetadata = ServiceManager::get($service);

            if (! $serviceMetadata && $registry = ServiceManager::getDefaultRegistry()) {
                $serviceMetadata = [ServiceManager::REGISTRY => $registry];
            }

            throw_if(
                ! $serviceMetadata,
                new ClientException(sprintf('Service %s does not register yet.', $service))
            );

            // when $transporter is string
            if (is_string($transporter)) {
                $protocol = $transporter;
                $transporter = null;
            }

            if (isset($serviceMetadata[ServiceManager::TRANSPORTER])) { // preference to using transporter
                $transporter = $serviceMetadata[ServiceManager::TRANSPORTER];

                throw_if(
                    ! ($transporter instanceof TransporterInterface),
                    new ClientException(sprintf('Service %s\'s transporter must be instanceof %s.', $service, TransporterInterface::class))
                );
            } elseif (isset($serviceMetadata[ServiceManager::REGISTRY])) { // using registry
                /** @var RegistryInterface $registry */
                $registry = $serviceMetadata[ServiceManager::REGISTRY];

                throw_if(
                    ! ($registry instanceof RegistryInterface),
                    new ClientException(sprintf('Service %s\'s registry must be instanceof %s.', $service, RegistryInterface::class))
                );

                $transporter = $registry->getTransporter($service, $protocol);
            }

            throw_if(! $transporter, new ClientException(sprintf('Service %s\'s transporter does not register yet.', $service)));

            if (isset($serviceMetadata[ServiceManager::PACKER]) && $serviceMetadata[ServiceManager::PACKER] instanceof PackerInterface) {
                $packer = $serviceMetadata[ServiceManager::PACKER];
            }

            if (isset($serviceMetadata[ServiceManager::DATA_FORMATTER]) && $serviceMetadata[ServiceManager::DATA_FORMATTER] instanceof DataFormatterInterface) {
                $dataFormatter = $serviceMetadata[ServiceManager::DATA_FORMATTER];
            }

            if (isset($serviceMetadata[ServiceManager::PATH_GENERATOR]) && $serviceMetadata[ServiceManager::PATH_GENERATOR] instanceof PathGeneratorInterface) {
                $pathGenerator = $serviceMetadata[ServiceManager::PATH_GENERATOR];
            }

            if (isset($serviceMetadata[ServiceManager::TRIES])) {
                $tries = (int) $serviceMetadata[ServiceManager::TRIES];
            }
        }

        return new Client($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }
}
