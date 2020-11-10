<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf Jet-client.
 *
 * @link     https://github.com/huangdijia/jet-client
 * @document https://github.com/huangdijia/jet-client/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/jet-client/blob/main/LICENSE
 */
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
        $this->host = $host;
        $this->port = $port;
        $this->weight = $weight;
        $this->options = $options;
    }
}
