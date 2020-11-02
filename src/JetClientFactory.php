<?php

class JetClientFactory
{
    /**
     * Create a client
     * @param string $service
     * @param JetTransporterInterface $transporter
     * @return JetClient
     */
    public static function create($service, $transporter = null)
    {
        $serviceMetadata = JetServiceManager::get($service);

        $packer        = null;
        $dataFormatter = null;
        $pathGenerator = null;

        if (is_null($transporter)) {
            if (
                isset($serviceMetadata[JetServiceManager::CONSULPORTER])
                && $serviceMetadata[JetServiceManager::CONSULPORTER] instanceof JetConsulporterInterface
            ) {
                /** @var JetConsulporterInterface $consulporter */
                $consulporter = $serviceMetadata[JetServiceManager::CONSULPORTER];
                $transporter  = $consulporter->getTransporter($service);
            } else {
                JetUtil::throwIf(
                    !$serviceMetadata,
                    new JetClientException(sprintf('Service %s does not register yet.', $service))
                );

                JetUtil::throwIf(
                    !isset($serviceMetadata[JetServiceManager::TRANSPORTER]),
                    new JetClientException(sprintf('Service %s transporter does not register yet.', $service))
                );

                JetUtil::throwIf(
                    !($serviceMetadata[JetServiceManager::TRANSPORTER] instanceof JetTransporterInterface),
                    new JetClientException(sprintf('Service %s transporter does not instanceof %s', $service, 'JetTransporterInterface'))
                );

                /** @var JetTransporterInterface $transporter */
                $transporter = $serviceMetadata[JetServiceManager::TRANSPORTER];
            }

            if (isset($serviceMetadata[JetServiceManager::PACKER]) && $serviceMetadata[JetServiceManager::PACKER] instanceof JetPackerInterface) {
                $packer = $serviceMetadata[JetServiceManager::PACKER];
            }

            if (isset($serviceMetadata[JetServiceManager::DATA_FORMATTER]) && $serviceMetadata[JetServiceManager::DATA_FORMATTER] instanceof JetPackerInterface) {
                $dataFormatter = $serviceMetadata[JetServiceManager::DATA_FORMATTER];
            }

            if (isset($serviceMetadata[JetServiceManager::PATH_GENERATOR]) && $serviceMetadata[JetServiceManager::PATH_GENERATOR] instanceof JetPackerInterface) {
                $pathGenerator = $serviceMetadata[JetServiceManager::PATH_GENERATOR];
            }
        }

        return new JetClient($service, $transporter, $packer, $dataFormatter, $pathGenerator);
    }
}
