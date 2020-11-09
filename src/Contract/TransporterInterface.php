<?php

namespace Huangdijia\Jet\Contract;

interface TransporterInterface
{
    /**
     * @param string $data 
     * @return void 
     */
    public function send(string $data);

    /**
     * @return string 
     */
    public function recv();

    /**
     * @return LoadBalancerInterface|null
     */
    public function getLoadBalancer(): ?LoadBalancerInterface;

    /**
     * @param LoadBalancerInterface|null $loadBalancer 
     * @return TransporterInterface 
     */
    public function setLoadBalancer(?LoadBalancerInterface $loadBalancer): TransporterInterface;

}