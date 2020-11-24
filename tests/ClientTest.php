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

use Huangdijia\Jet\ClientFactory;
use Huangdijia\Jet\RegistryManager;
use Huangdijia\Jet\ServiceManager;

/**
 * @internal
 * @coversNothing
 */
class ClientTest extends TestCase
{
    private $service = 'CalculatorService';

    public function testCalculatorServiceByRegistry()
    {
        $registry = $this->createRegistry();

        RegistryManager::register(RegistryManager::DEFAULT, $registry);

        // ServiceManager::register($this->service, [
        //     ServiceManager::REGISTRY => RegistryManager::DEFAULT,
        // ]);

        $client = ClientFactory::create($this->service, 'jsonrpc-http');

        $a = rand(1, 99);
        $b = rand(1, 99);

        $this->assertSame($a + $b, $client->add($a, $b));

        $client = ClientFactory::create($this->service, 'jsonrpc');

        $a = rand(1, 99);
        $b = rand(1, 99);

        $this->assertSame($a + $b, $client->add($a, $b));
    }

    public function testCalculatorServiceByGuzzleHttpTransporter()
    {
        $client = ClientFactory::create($this->service, $this->createGuzzleHttpTransporter());

        $a = rand(1, 99);
        $b = rand(1, 99);

        $this->assertSame($a + $b, $client->add($a, $b));
    }

    public function testCalculatorServiceByStreamSocketTransporter()
    {
        $client = ClientFactory::create($this->service, $this->createStreamSocketTransporter());

        $a = rand(1, 99);
        $b = rand(1, 99);

        $this->assertSame($a + $b, $client->add($a, $b));
    }
}
