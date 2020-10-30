<?php

class JetLoadBalancerNode
{
    /**
     * @var string
     */
    public $host;
    /**
     * @var int
     */
    public $port;
    /**
     * @var int
     */
    public $weight;
    /**
     * @var array
     */
    public $options;

    public function __construct($host = '127.0.0.1', $port = 9501, $weight = 1, $options = array())
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->weight  = $weight;
        $this->options = $options;
    }
}
