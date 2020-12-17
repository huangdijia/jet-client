<?php

interface JetRegistryInterface
{
    /**
     * @param JetLoadBalancerInterface|null $loadBalancer
     * @return void
     */
    public function setLoadBalancer($loadBalancer);

    /**
     * @return JetLoadBalancerInterface|null
     */
    public function getLoadBalancer();

    /**
     * @return array
     */
    public function getServices();

    /**
     * @param string $service
     * @param string|null $protocol
     * @return array|JetLoadBalancerNode[]
     */
    public function getServiceNodes($service, $protocol = null);

    /**
     * @param string $service
     * @param string|null $protocol
     * @param int $timeout
     * @return JetTransporterInterface
     */
    public function getTransporter($service, $protocol = null, $timeout  = 1);

    /**
     * @param array|string|null $service 
     * @return void 
     */
    public function register($service = null);
}
