<?php

namespace Huangdijia\Jet\Test;

use Huangdijia\Jet\Registry\ConsulRegistry;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    private $registryHost = '127.0.0.1';
    private $registryPort = 8500;

    public function testGetServices()
    {
        $registry = new ConsulRegistry($this->registryHost, $this->registryPort);
        $services = $registry->getServices();

        $this->assertCount(2, count($services));
    }
}
