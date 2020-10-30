<?php

interface JetLoadBalancerInterface
{
    /**
     * Select an item via the load balancer.
     * @return JetLoadBalancerNode
     */
    public function select();

    /**
     * @param JetLoadBalancerNode $nodes
     * @return $this
     */
    public function setNodes($nodes);

    /**
     * @return JetLoadBalancerNode[] $nodes
     */
    public function getNodes();
}