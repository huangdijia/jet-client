<?php

class JetRandomLoadBalancer extends AbstractJetLoadBalancer
{
    /**
     * Select an item via the load balancer.
     * @return JetLoadBalancerNode
     */
    public function select()
    {
        if (empty($this->nodes)) {
            throw new \RuntimeException('Cannot select any node from load balancer.');
        }

        $key = array_rand($this->nodes);

        return $this->nodes[$key];
    }
}
