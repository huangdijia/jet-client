<?php

namespace Huangdijia\Jet\LoadBalancer;

use RuntimeException;

class RoundRobin extends AbstractLoadBalancer
{
    /**
     * @var int
     */
    private static $current = 0;

    /**
     * Select an item via the load balancer.
     * @return Node
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
