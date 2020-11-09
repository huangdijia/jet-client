<?php

namespace Huangdijia\Jet\Contract;

use Huangdijia\Jet\LoadBalancer\Node;

interface LoadBalancerInterface
{
    /**
     * Select an item via the load balancer.
     * @return Node
     */
    public function select();

    /**
     * @param Node $nodes
     * @return $this
     */
    public function setNodes(array $nodes);

    /**
     * @return Node[] $nodes
     */
    public function getNodes();
}