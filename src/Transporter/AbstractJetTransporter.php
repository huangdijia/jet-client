<?php

abstract class AbstractJetTransporter implements JetTransporterInterface
{
    protected $host;
    protected $port;
    protected $timeout;

    /**
     * @var null|JetLoadBalancerInterface
     */
    protected $loadBalancer;

    public function __construct($host = '127.0.0.1', $port = 9502, $timeout = 1.0)
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
    }

    /**
     * @return null|JetLoadBalancerInterface
     */
    public function getLoadBalancer()
    {
        return $this->loadBalancer;
    }

    /**
     * @param JetLoadBalancerInterface $loadBalancer
     * @return $this
     */
    public function setLoadBalancer($loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;

        return $this;
    }
}
