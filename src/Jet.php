<?php

class Jet
{
    const TRANSPORTER = 't';

    protected static $consuls   = array();
    protected static $protocols = array();

    /**
     *
     * @param string $uri
     * @param int $timeout
     * @return void
     */
    public static function addConsul($uri, $timeout = 1)
    {
        self::$consuls[] = array('uri' => $uri, 'timeout' => $timeout);
    }

    /**
     * @param string $protocol
     * @param array $metadatas
     * @return void
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function registerProtocol($protocol, $metadatas = array())
    {
        JetUtil::throwIf(self::isProtocolRegistered($protocol), new RuntimeException("{$protocol} has registered"));

        self::$protocols[$protocol] = $metadatas;
    }

    /**
     * @param string $protocol
     * @return bool
     */
    public static function isProtocolRegistered($protocol)
    {
        return isset(self::$protocols[$protocol]);
    }

    /**
     * @param mixed $protocol
     * @return array|null
     */
    public static function getProtocol($protocol)
    {
        return isset(self::$protocols[$protocol]) ? self::$protocols[$protocol] : null;
    }

    /**
     * Create a client
     * @param string $service
     * @param AbstractJetTransporter|string|null $transporter
     * @return JetClient
     */
    public static function create($service, $transporter = null)
    {
        if (!($transporter instanceof AbstractJetTransporter)) {
            $protocol    = $transporter;
            $transporter = self::createTransporter($service, $protocol);
        }

        return new JetClient($service, $transporter);
    }

    /**
     * @param mixed $service
     * @param string|null $protocol
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function getServiceNodes($service, $protocol = null)
    {
        $consulNodes = self::$consuls;

        JetUtil::throwIf(count($consulNodes) <= 0, new RuntimeException('Consul nodes not found!'));

        $consulLoadBalancer = JetUtil::tap(new JetRoundRobinLoadBalancer(), function ($balancer) use ($consulNodes) {
            $balancer->setNodes(JetUtil::value(function () use ($consulNodes) {
                $nodes = array();

                foreach ($consulNodes as $node) {
                    $nodes[] = new JetLoadBalancerNode('', '', 1, $node);
                }

                return $nodes;
            }));
        });

        return JetUtil::retry(count($consulNodes), function () use ($consulLoadBalancer, $service, $protocol) {
            $consulNode   = $consulLoadBalancer->select();
            $consulHealth = new JetConsulHealth($consulNode->options);

            return JetUtil::with($consulHealth->service($service), function ($nodes) use ($protocol) {
                $passings = array();

                foreach ($nodes as $node) {
                    if (
                        isset($node['Checks'])
                        && isset($node['Checks'][1])
                        && isset($node['Checks'][1]['Status'])
                        && $node['Checks'][1]['Status'] == 'passing'
                    ) {
                        if (!is_null($protocol) && $protocol != $node['Service']['Meta']['Protocol']) {
                            continue;
                        }

                        $passings[] = new JetLoadBalancerNode(
                            $node['Service']['Address'],
                            $node['Service']['Port'],
                            1,
                            array(
                                'type'     => $node['Checks'][1]['Type'],
                                'protocol' => $node['Service']['Meta']['Protocol'],
                            )
                        );
                    }
                }

                return $passings;
            });
        });
    }

    /**
     * Create a transporter
     * @param string $service
     * @param string|null $protocol
     * @return AbstractJetTransporter
     */
    public static function createTransporter($service, $protocol = null)
    {
        $nodes = self::getServiceNodes($service, $protocol);

        JetUtil::throwIf(count($nodes) <= 0, new RuntimeException('Service nodes not found!'));

        $transporter     = null;
        $serviceBalancer = new JetRoundRobinLoadBalancer();
        $serviceBalancer->setNodes($nodes);

        if ($protocol && $metadatas = self::getProtocol($protocol)) {
            if (isset($metadatas[self::TRANSPORTER]) && $metadatas[self::TRANSPORTER] instanceof AbstractJetTransporter) {
                $transporter = $metadatas[self::TRANSPORTER];
            }
        }

        if (is_null($transporter)) {
            $node = $serviceBalancer->select();

            if ($node->options['type'] == 'tcp') {
                $transporter = new JetStreamSocketTransporter($node->host, $node->port);
            } else {
                $transporter = new JetCurlHttpTransporter($node->host, $node->port);
            }
        }

        if (count($nodes) > 1) {
            $transporter->setLoadBalancer($serviceBalancer);
        }

        return $transporter;
    }
}
