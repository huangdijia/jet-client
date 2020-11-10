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
namespace Huangdijia\Jet\Test;

use Huangdijia\Jet\Registry\ConsulRegistry;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegistryTest extends TestCase
{
    private $registryHost = '127.0.0.1';

    private $registryPort = 8500;

    public function testGetServices()
    {
        $registry = new ConsulRegistry($this->registryHost, $this->registryPort, 1);
        $services = $registry->getServices();

        $this->assertGreaterThan(2, count($services));
    }
}
