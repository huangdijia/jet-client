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

use Huangdijia\Jet\ClientFactory;
use Huangdijia\Jet\Registry\ConsulRegistry;
use Huangdijia\Jet\ServiceManager;
use Huangdijia\Jet\Transporter\GuzzleHttpTransporter;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ServiceTest extends TestCase
{
    private $service = 'Calculator';

    private $registryHost = '127.0.0.1';

    private $registryPort = 8500;

    private $serviceHost = '127.0.0.1';

    private $serviceHttpPort = 9502;

    private $serviceTcpPort = 9503;

    public function testCalculatorServiceByRegistry()
    {
        $registry = new ConsulRegistry($this->registryHost, $this->registryPort, 1);

        ServiceManager::register($this->service, [
            ServiceManager::REGISTRY => $registry,
        ]);

        $client = ClientFactory::create($this->service);

        $a = rand(1, 99);
        $b = rand(1, 99);

        $this->assertSame($a + $b, $client->add($a, $b));
    }

    public function testCalculatorServiceByHttpTransporter()
    {
        $client = ClientFactory::create($this->service, new GuzzleHttpTransporter($this->serviceHost, $this->serviceHttpPort));

        $a = rand(1, 99);
        $b = rand(1, 99);

        $this->assertSame($a + $b, $client->add($a, $b));
    }

    public function testCalculatorServiceByTcpTransporter()
    {
        $client = ClientFactory::create($this->service, new GuzzleHttpTransporter($this->serviceHost, $this->serviceTcpPort));

        $a = rand(1, 99);
        $b = rand(1, 99);

        $this->assertSame($a + $b, $client->add($a, $b));
    }
}
