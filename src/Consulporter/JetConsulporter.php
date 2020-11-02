<?php

class JetConsulporter implements JetConsulporterInterface
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
        return $this->loadBalancer;
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

    /**
     * @param mixed $service
     * @param string|null $protocol
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getServiceNodes($service, $protocol = null)
    {
        $loadBalancer = $this->getLoadBalancer();

        if (!$loadBalancer) {
            $nodes = array(
                new JetLoadBalancerNode('', '', 1, array(
                    'uri'     => sprintf('http://%s:%s', $this->host, $this->port),
                    'timeout' => $this->timeout,
                )),
            );
            $loadBalancer = new JetRoundRobinLoadBalancer($nodes);
            $this->setLoadBalancer($loadBalancer);
        }

        return JetUtil::retry(count($loadBalancer->getNodes()), function () use ($loadBalancer, $service, $protocol) {
            $consulNode = $loadBalancer->select();

            if (!isset($consulNode->options['uri'])) {
                $consulNode->options['uri'] = sprintf('http://%s:%s', $consulNode->host, $consulNode->port);
            }
            if (!isset($consulNode->options['timeout'])) {
                $consulNode->options['timeout'] = 1;
            }

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
}
