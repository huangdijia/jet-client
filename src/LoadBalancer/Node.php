<?php

namespace Huangdijia\Jet\LoadBalancer;

class Node
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

    public function __construct(string $host = '127.0.0.1', int $port = 9501, int $weight = 1, array $options = [])
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->weight  = $weight;
        $this->options = $options;
    }
}
