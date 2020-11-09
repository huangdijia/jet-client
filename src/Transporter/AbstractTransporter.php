<?php

namespace Huangdijia\Jet\Transporter;

use Huangdijia\Jet\Contract\LoadBalancerInterface;
use Huangdijia\Jet\Contract\TransporterInterface;

abstract class AbstractTransporter implements TransporterInterface
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
     * @var null|LoadBalancerInterface
     */
    protected $loadBalancer;

    /**
     * @param string $host
     * @param int $port
     * @param int $timeout
     */
    public function __construct(string $host = '127.0.0.1', int $port = 9502, int $timeout = 1)
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
    }

    public function getLoadBalancer(): ?LoadBalancerInterface
    {
        return $this->loadBalancer;
    }

    public function setLoadBalancer(?LoadBalancerInterface $loadBalancer): TransporterInterface
    {
        $this->loadBalancer = $loadBalancer;

        return $this;
    }
}
