<?php

namespace Huangdijia\Jet\Contract;

interface TransporterInterface
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
     * @return LoadBalancerInterface|null
     */
    public function getLoadBalancer();

    /**
     * @param LoadBalancerInterface|null $loadBalancer 
     * @return TransporterInterface 
     */
    public function setLoadBalancer(?LoadBalancerInterface $loadBalancer);

}