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
require_once __DIR__ . '/../vendor/autoload.php';

use Huangdijia\Jet\Client;
use Huangdijia\Jet\ClientFactory;
use Huangdijia\Jet\Facade;
use Huangdijia\Jet\Registry\ConsulRegistry;
use Huangdijia\Jet\ServiceManager;
use Huangdijia\Jet\Transporter\GuzzleHttpTransporter;

$configs = include __DIR__ . '/config.php';
$host = array_get($configs, 'consul.host', '127.0.0.1');
$port = array_get($configs, 'consul.port', 8500);

$registry = new ConsulRegistry($host, $port, 1);

ServiceManager::register('CalculatorService', [
    // JetServiceManager::TRANSPORTER => new JetGuzzleHttpTransporter('127.0.0.1', 9502),
    // JetServiceManager::TRANSPORTER => $registry->getTransporter('CalculatorService'),
    ServiceManager::REGISTRY => $registry,
]);
ServiceManager::register('CalculatorService:tcp', [
    // JetServiceManager::TRANSPORTER => new JetStreamSocketTransporter('127.0.0.1', 9503),
    // JetServiceManager::TRANSPORTER => $registry->getTransporter('CalculatorTcpService', 'jsonrpc'),
    ServiceManager::REGISTRY => $registry,
]);

/**
 * @method static int add(int $a, int $b)
 */
class Calculator extends Facade
{
    protected static function getFacadeAccessor()
    {
        // return JetClientFactory::create('CalculatorService');
        return 'CalculatorService';
    }
}

var_dump(Calculator::add(rand(0, 100), rand(0, 100)));

/**
 * @method int add(int$a, int$b)
 */
class CalculatorService extends Client
{
    public function __construct($service = 'CalculatorService', $transporter = null, $packer = null, $dataFormatter = null, $pathGenerator = null)
    {
        $transporter = new GuzzleHttpTransporter('127.0.0.1', 9502);

        parent::__construct($service, $transporter, $packer, $dataFormatter, $pathGenerator);
    }
}

$service = new CalculatorService();
var_dump($service->add(rand(0, 100), rand(0, 100)));

$client = ClientFactory::create('CalculatorService:tcp', 'jsonrpc');
var_dump($client->add(rand(0, 100), rand(0, 100)));

$client = ClientFactory::create('CalculatorService', new GuzzleHttpTransporter('127.0.0.1', 9502));
var_dump($client->add(rand(0, 100), rand(0, 100)));
