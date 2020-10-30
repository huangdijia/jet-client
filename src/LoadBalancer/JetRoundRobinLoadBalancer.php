<?php

class JetRoundRobinLoadBalancer extends AbstractJetLoadBalancer
{
    /**
     * @var int
     */
    private static $current = 0;

    /**
     * Select an item via the load balancer.
     * @return JetLoadBalancerNode
     */
    public function select()
    {
        $count = count($this->nodes);

        if ($count <= 0) {
            throw new RuntimeException('Nodes missing.');
        }

        $item = $this->nodes[self::$current % $count];
        ++self::$current;

        return $item;
    }
}
