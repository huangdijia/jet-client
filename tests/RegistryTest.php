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
namespace Huangdijia\Jet\Tests;

use Huangdijia\Jet\Contract\RegistryInterface;
use Huangdijia\Jet\RegistryManager;
use Huangdijia\Jet\ServiceManager;

/**
 * @internal
 * @coversNothing
 */
class RegistryTest extends TestCase
{
    public function testGetServices()
    {
        $registry = $this->createRegistry();
        $services = $registry->getServices();

        $this->assertIsArray($services);
        $this->assertContains('consul', $services);
    }

    public function testRegisterService()
    {
        $registry = $this->createRegistry();
        $registry->register();
        $datameta = ServiceManager::get('consul');

        $this->assertIsArray($datameta);
        $this->assertArrayHasKey(ServiceManager::REGISTRY, $datameta);
        $this->assertInstanceOf(RegistryInterface::class, $datameta[ServiceManager::REGISTRY]);
    }

    public function testRegisterDefaultRegistry()
    {
        $registry = $this->createRegistry();

        ServiceManager::registerDefaultRegistry($registry, true);

        $this->assertInstanceOf(RegistryInterface::class, ServiceManager::getDefaultRegistry());
    }

    public function testRegistryManager()
    {
        $registry = $this->createRegistry();

        RegistryManager::register(RegistryManager::DEFAULT, $registry, true);

        $this->assertInstanceOf(RegistryInterface::class, RegistryManager::get(RegistryManager::DEFAULT));
    }
}
