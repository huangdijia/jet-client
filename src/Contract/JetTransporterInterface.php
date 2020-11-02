<?php

interface JetTransporterInterface
{
    /**
     * @param string $data 
     * @return void 
     */
    public function send($data);

    /**
     * @return string 
     */
    public function recv();

    /**
     * @return JetLoadBalancerInterface 
     */
    public function getLoadBalancer();

    /**
     * @param JetLoadBalancerInterface $loadBalancer 
     * @return JetTransporterInterface 
     */
    public function setLoadBalancer($loadBalancer);

}