<?php

namespace Huangdijia\Jet\LoadBalancer;

class Random extends AbstractLoadBalancer
{
    /**
     * Select an item via the load balancer.
     * @return Node
     */
    public function select(): Node
    {
        if (empty($this->nodes)) {
            throw new \RuntimeException('Cannot select any node from load balancer.');
        }

        $key = array_rand($this->nodes);

        return $this->nodes[$key];
    }
}
