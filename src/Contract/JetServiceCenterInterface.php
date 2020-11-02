<?php

interface JetServiceCenterInterface
{
    /**
     * @param string $service 
     * @param string|null $protocol 
     * @return JetTransporterInterface 
     */
    public function getTransporter($service, $protocol = null);

    /**
     * @param string $service 
     * @param string|null $protocol 
     * @return array|JetLoadBalancerNode[]
     */
    public function getServiceNodes($service, $protocol = null);

    /**
     * @param JetLoadBalancerInterface|null $loadBalancer 
     * @return void 
     */
    public function setLoadBalancer($loadBalancer);

    /**
     * @return JetLoadBalancerInterface|null  
     */
    public function getLoadBalancer();
}