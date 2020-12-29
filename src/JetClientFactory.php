<?php

class JetClientFactory
{
    /**
     * timeout
     * @var int
     */
    protected static $timeout = 5;

    /**
     * Set timeout
     * @param mixed $timeout
     * @return void
     */
    public function setTimeout($timeout)
    {
        $timeout = (int) $timeout;

        if ($timeout < 0) {
            return;
        }

        self::$timeout = $timeout;
    }
    /**
     * Create a client
     *
     * @param string $service
     * @param JetTransporterInterface|string|null $transporter
     * @param JetPackerInterface|null $packer
     * @param JetDataFormatterInterface|null $dataFormatter
     * @param JetPathGeneratorInterface|null $pathGenerator
     * @param int|null $tries
     * @return JetClient
     * @throws JetClientException
     */
    public static function create($service, $transporter = null, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        $protocol = null;
        $timeout  = self::$timeout;

        // create by transporter
        if ($transporter instanceof JetTransporterInterface) {
            return static::createWithTransporter($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
        }

        // when $transporter is number or string
        if (is_numeric($transporter)) {
            $timeout     = $transporter;
            $transporter = null;
        } elseif (is_string($transporter)) {
            $protocol    = $transporter;
            $transporter = null;
        }

        $metadatas = JetServiceManager::get($service);

        if (!$metadatas) {
            if ($registry = JetRegistryManager::get(JetRegistryManager::DEFAULT_REGISTRY)) {
                return self::createWithRegistry($service, $registry, $protocol, $packer, $dataFormatter, $pathGenerator, $tries);
            }

            throw new JetClientException(sprintf('Service %s does not register yet.', $service));
        }

        if (isset($metadatas[JetServiceManager::TRANSPORTER])) { // preference to using transporter
            /** @var TransporterInterface $transporter */
            $transporter = $metadatas[JetServiceManager::TRANSPORTER];
        } elseif (isset($metadatas[JetServiceManager::REGISTRY])) { // using registry
            $registry = $metadatas[JetServiceManager::REGISTRY];

            /** @var string|RegistryInterface $registry */
            if (is_string($registry)) {
                if (!JetRegistryManager::isRegistered($registry)) {
                    throw new InvalidArgumentException(sprintf('Registry %s does not registered yet.', $registry));
                }

                $registry = JetRegistryManager::get($registry);
            }

            /** @var RegistryInterface $registry */
            $transporter = $registry->getTransporter($service, $protocol, $timeout);
        }

        if (!$transporter) {
            throw new JetClientException(sprintf('Transporter of service %s does not register yet.', $service));
        }

        if (is_null($packer)) {
            $packer = isset($metadatas[JetServiceManager::PACKER]) ? $metadatas[JetServiceManager::PACKER] : null;
        }

        if (is_null($dataFormatter)) {
            $dataFormatter = isset($metadatas[JetServiceManager::DATA_FORMATTER]) ? $metadatas[JetServiceManager::DATA_FORMATTER] : null;
        }

        if (is_null($pathGenerator)) {
            $pathGenerator = isset($metadatas[JetServiceManager::PATH_GENERATOR]) ? $metadatas[JetServiceManager::PATH_GENERATOR] : null;
        }

        if (is_null($tries)) {
            $tries = isset($metadatas[JetServiceManager::TRIES]) ? $metadatas[JetServiceManager::TRIES] : 1;
        }

        return static::createWithTransporter($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }

    /**
     * Create a client with transporter.
     *
     * @param string $service
     * @param JetTransporterInterface $transporter
     * @param JetPackerInterface|null $packer
     * @param JetDataFormatterInterface|null $dataFormatter
     * @param JetPathGeneratorInterface|null $pathGenerator
     * @param int|null $tries
     * @return JetClient
     */
    public static function createWithTransporter($service, $transporter, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        return new JetClient($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }

    /**
     * Create a client with registry.
     *
     * @param string $service
     * @param JetRegistryInterface $registry
     * @param string|null $protocol
     * @param JetPackerInterface|null $packer
     * @param JetDataFormatterInterface|null $dataFormatter
     * @param JetPathGeneratorInterface|null $pathGenerator
     * @param int|null $tries
     * @return JetClient
     */
    public static function createWithRegistry($service, $registry, $protocol = null, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        $transporter = $registry->getTransporter($service, $protocol, self::$timeout);

        return new JetClient($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }
}
