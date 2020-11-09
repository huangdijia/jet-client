<?php

class JetClientFactory
{
    /**
     * Create a client
     * @param string $service
     * @param JetTransporterInterface|string|null $transporter
     * @return JetClient
     */
    public static function create($service, $transporter = null)
    {
        $packer        = null;
        $dataFormatter = null;
        $pathGenerator = null;
        $protocol      = null;
        $tries         = 1;

        if (!($transporter instanceof JetTransporterInterface)) {
            $serviceMetadata = JetServiceManager::get($service);

            JetUtil::throwIf(
                !$serviceMetadata,
                new JetClientException(sprintf('Service %s does not register yet.', $service))
            );

            // when $transporter is string
            if (is_string($transporter)) {
                $protocol    = $transporter;
                $transporter = null;
            }

            if (isset($serviceMetadata[JetServiceManager::TRANSPORTER])) { // preference to using transporter
                $transporter = $serviceMetadata[JetServiceManager::TRANSPORTER];

                JetUtil::throwIf(
                    !($transporter instanceof JetTransporterInterface),
                    new JetClientException(sprintf('Service %s\'s transporter must be instanceof JetTransporterInterface.', $service))
                );
            } elseif (isset($serviceMetadata[JetServiceManager::REGISTRY])) { // using service center
                /** @var JetRegistryInterface $registry */
                $registry = $serviceMetadata[JetServiceManager::REGISTRY];

                JetUtil::throwIf(
                    !($registry instanceof JetRegistryInterface),
                    new JetClientException(sprintf('Service %s\'s service center must be instanceof JetRegistryInterface.', $service))
                );

                $transporter = $registry->getTransporter($service, $protocol);
            }

            JetUtil::throwIf(!$transporter, new JetClientException(sprintf('Service %s\'s transporter does not register yet.', $service)));

            if (isset($serviceMetadata[JetServiceManager::PACKER]) && $serviceMetadata[JetServiceManager::PACKER] instanceof JetPackerInterface) {
                $packer = $serviceMetadata[JetServiceManager::PACKER];
            }

            if (isset($serviceMetadata[JetServiceManager::DATA_FORMATTER]) && $serviceMetadata[JetServiceManager::DATA_FORMATTER] instanceof JetPackerInterface) {
                $dataFormatter = $serviceMetadata[JetServiceManager::DATA_FORMATTER];
            }

            if (isset($serviceMetadata[JetServiceManager::PATH_GENERATOR]) && $serviceMetadata[JetServiceManager::PATH_GENERATOR] instanceof JetPackerInterface) {
                $pathGenerator = $serviceMetadata[JetServiceManager::PATH_GENERATOR];
            }

            if (isset($serviceMetadata[JetServiceManager::TRIES])) {
                $tries = (int) $serviceMetadata[JetServiceManager::TRIES];
            }
        }

        return new JetClient($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }
}
