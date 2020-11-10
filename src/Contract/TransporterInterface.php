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
namespace Huangdijia\Jet\Contract;

interface TransporterInterface
{
    public function send(string $data);

    /**
     * @return string
     */
    public function recv();

    public function getLoadBalancer(): ?LoadBalancerInterface;

    public function setLoadBalancer(?LoadBalancerInterface $loadBalancer): TransporterInterface;
}
