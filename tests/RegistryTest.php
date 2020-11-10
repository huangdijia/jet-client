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
        $this->assertContains('CalculatorService', $services);
    }
}
