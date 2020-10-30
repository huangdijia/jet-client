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
            $protocol     = $transporter;
            $consulNodes  = self::$consuls;

            /** @var JetConsulporter $consulporter */
            $consulporter = JetUtil::tap(new JetConsulporter(), function ($consulporter) use ($consulNodes) {
                /** @var JetConsulporter $consulporter */
                $loadBalancer = JetUtil::tap(new JetRoundRobinLoadBalancer(), function ($balancer) use ($consulNodes) {
                    $balancer->setNodes(JetUtil::value(function () use ($consulNodes) {
                        $nodes = array();

                        foreach ($consulNodes as $node) {
                            $nodes[] = new JetLoadBalancerNode('', '', 1, $node);
                        }

                        return $nodes;
                    }));
                });

                $consulporter->setLoadBalancer($loadBalancer);
            });

            $transporter = $consulporter->getTransporter($service, $protocol);
        }

        return new JetClient($service, $transporter);
    }
}
