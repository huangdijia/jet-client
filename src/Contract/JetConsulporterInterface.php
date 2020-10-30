<?php

interface JetConsulporterInterface
{
    /**
     * @param string $service 
     * @param string|null $protocol 
     * @return JetTransporterInterface 
     */
    public function getTransporter($service, $protocol = null);

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