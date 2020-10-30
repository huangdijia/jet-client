<?php

abstract class AbstractJetLoadBalancer implements JetLoadBalancerInterface
{
    /**
     * @var JetLoadBalancerNode[]
     */
    protected $nodes;

    /**
     * @param JetLoadBalancerNode[] $nodes
     */
    public function __construct($nodes = array())
    {
        $this->nodes = $nodes;
    }

    /**
     * @param JetLoadBalancerNode[] $nodes
     * @return $this
     */
    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
        return $this;
    }

    /**
     * @return JetLoadBalancerNode[] 
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}