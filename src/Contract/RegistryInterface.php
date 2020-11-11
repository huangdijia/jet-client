<?php

declare(strict_types=1);
/**
 * This file is part of Jet-Client.
 *
 * @link     https://github.com/huangdijia/jet-client
 * @document https://github.com/huangdijia/jet-client/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/jet-client/blob/main/LICENSE
 */
namespace Huangdijia\Jet\Contract;

use Huangdijia\Jet\LoadBalancer\Node;

interface RegistryInterface
{
    public function setLoadBalancer(?LoadBalancerInterface $loadBalancer);

    /**
     * @return null|LoadBalancerInterface
     */
    public function getLoadBalancer();

    /**
     * @return array
     */
    public function getServices();

    /**
     * @return array|Node[]
     */
    public function getServiceNodes(string $service, ?string $protocol = null);

    /**
     * @return TransporterInterface
     */
    public function getTransporter(string $service, ?string $protocol = null);

    /**
     * @param null|array|string $service
     */
    public function register($service = null);
}
