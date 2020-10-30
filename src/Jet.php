<?php

class Jet
{
    protected static $consuls = array();

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
     * Create a transporter
     * @param string $service
     * @param string|null $protocol
     * @return AbstractJetTransporter
     */
    public static function createTransporter($service, $protocol = null)
    {
        $consulNodes = self::$consuls;
        JetUtil::throwIf(count($consulNodes) <= 0, new RuntimeException('Consul nodes not found!'));

        $consulLoadBalancer = new JetRoundRobinLoadBalancer();
        $consulLoadBalancer->setNodes(JetUtil::value(function () use ($consulNodes) {
            $nodes = array();

            foreach ($consulNodes as $node) {
                $nodes[] = new JetLoadBalancerNode('', '', 1, $node);
            }

            return $nodes;
        }));

        $nodes = JetUtil::retry(count($consulNodes), function () use ($consulLoadBalancer, $service, $protocol) {
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

        JetUtil::throwIf(count($nodes) <= 0, new RuntimeException('Service nodes not found!'));

        $serviceBalancer = new JetRoundRobinLoadBalancer();
        $serviceBalancer->setNodes($nodes);

        $node = $serviceBalancer->select();

        if ($node->options['type'] == 'tcp') {
            $transporter = new JetStreamSocketTransporter($node->host, $node->port);
        } else {
            $transporter = new JetCurlHttpTransporter($node->host, $node->port);
        }

        if (count($nodes) > 1) {
            $transporter->setLoadBalancer($serviceBalancer);
        }

        return $transporter;
    }
}
