<?php

class JetConsulRegistry implements JetRegistryInterface
{
    /**
     * @var string
     */
    protected $host;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var int
     */
    protected $timeout;
    /**
     * @var JetLoadBalancerInterface
     */
    protected $loadBalancer;

    public function __construct($host = '127.0.0.1', $port = 8500, $timeout = 1)
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
    }

    public function setLoadBalancer($loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;
    }

    public function getLoadBalancer()
    {
        if (!$this->loadBalancer) {
            $this->loadBalancer = new JetRoundRobinLoadBalancer();
            $this->loadBalancer->setNodes(array(
                new JetLoadBalancerNode('', '', 1, array(
                    'uri'     => sprintf('http://%s:%s', $this->host, $this->port),
                    'timeout' => $this->timeout,
                )),
            ));
        }

        return $this->loadBalancer;
    }

    public function getServices()
    {
        $loadBalancer = $this->getLoadBalancer();

        return JetUtil::retry(count($loadBalancer->getNodes()), function () use ($loadBalancer) {
            $node = $loadBalancer->select();

            if (!isset($node->options['uri'])) {
                $node->options['uri'] = sprintf('http://%s:%s', $node->host, $node->port);
            }
            if (!isset($node->options['timeout'])) {
                $node->options['timeout'] = 1;
            }

            $consulCatalog = new JetConsulCatalog($node->options);

            return JetUtil::with($consulCatalog->services()->throwIf()->json(), function ($services) {
                return array_keys($services);
            });
        });
    }

    public function getServiceNodes($service, $protocol = null)
    {
        $loadBalancer = $this->getLoadBalancer();

        return JetUtil::retry(count($loadBalancer->getNodes()), function () use ($loadBalancer, $service, $protocol) {
            $consulNode = $loadBalancer->select();

            if (!isset($consulNode->options['uri'])) {
                $consulNode->options['uri'] = sprintf('http://%s:%s', $consulNode->host, $consulNode->port);
            }
            if (!isset($consulNode->options['timeout'])) {
                $consulNode->options['timeout'] = 1;
            }

            $consulHealth = new JetConsulHealth($consulNode->options);

            return JetUtil::with($consulHealth->service($service)->throwIf()->json(), function ($serviceNodes) use ($protocol) {
                /** @var array $serviceNodes */
                $nodes = array();

                foreach ($serviceNodes as $node) {
                    if (JetUtil::arrayGet($node, 'Checks.1.Status') != 'passing') {
                        continue;
                    }

                    if (!is_null($protocol) && $protocol != JetUtil::arrayGet($node, 'Service.Meta.Protocol')) {
                        continue;
                    }

                    $nodes[] = new JetLoadBalancerNode(
                        JetUtil::arrayGet($node, 'Service.Address'),
                        JetUtil::arrayGet($node, 'Service.Port'),
                        1,
                        array(
                            'type'     => JetUtil::arrayGet($node, 'Checks.1.Type'),
                            'protocol' => JetUtil::arrayGet($node, 'Service.Meta.Protocol'),
                        )
                    );
                }

                return $nodes;
            });
        });
    }

    public function getTransporter($service, $protocol = null)
    {
        $nodes = $this->getServiceNodes($service, $protocol);

        JetUtil::throwIf(count($nodes) <= 0, new RuntimeException('Service nodes not found!'));

        $transporter     = null;
        $serviceBalancer = new JetRoundRobinLoadBalancer();
        $serviceBalancer->setNodes($nodes);

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
