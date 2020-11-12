<?php

class JetClientFactory
{
    public static function create($service, $transporter = null, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        $protocol = null;

        if ($transporter instanceof JetTransporterInterface) {
            return static::createWithTransporter($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
        }

        // when $transporter is string
        if (is_string($transporter)) {
            $protocol    = $transporter;
            $transporter = null;
        }

        $serviceMetadata = JetServiceManager::get($service);

        if (!$serviceMetadata) {
            if ($registry = JetServiceManager::getDefaultRegistry()) {
                return self::createWithRegistry($service, $registry, $protocol, $packer, $dataFormatter, $pathGenerator, $tries);
            }

            throw new JetClientException(sprintf('Service %s does not register yet.', $service));
        }

        if (isset($serviceMetadata[JetServiceManager::TRANSPORTER])) { // preference to using transporter
            /** @var TransporterInterface $transporter */
            $transporter = $serviceMetadata[JetServiceManager::TRANSPORTER];
        } elseif (isset($serviceMetadata[JetServiceManager::REGISTRY])) { // using registry
            /** @var RegistryInterface $registry */
            $registry    = $serviceMetadata[JetServiceManager::REGISTRY];
            $transporter = $registry->getTransporter($service, $protocol);
        }

        if (!$transporter) {
            throw new JetClientException(sprintf('Service %s\'s transporter does not register yet.', $service));
        }

        if (is_null($packer)) {
            $packer = isset($serviceMetadata[JetServiceManager::PACKER]) ? $serviceMetadata[JetServiceManager::PACKER] : null;
        }

        if (is_null($dataFormatter)) {
            $dataFormatter = isset($serviceMetadata[JetServiceManager::DATA_FORMATTER]) ? $serviceMetadata[JetServiceManager::DATA_FORMATTER] : null;
        }

        if (is_null($pathGenerator)) {
            $pathGenerator = isset($serviceMetadata[JetServiceManager::PATH_GENERATOR]) ? $serviceMetadata[JetServiceManager::PATH_GENERATOR] : null;
        }

        if (is_null($tries)) {
            $tries = isset($serviceMetadata[JetServiceManager::TRIES]) ? $serviceMetadata[JetServiceManager::TRIES] : 1;
        }

        return static::createWithTransporter($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }

    /**
     * Create a client with transporter.
     */
    public static function createWithTransporter($service, $transporter, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        return new JetClient($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }

    public static function createWithRegistry($service, $registry, $protocol = null, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        $transporter = $registry->getTransporter($service, $protocol);

        return new JetClient($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }
}