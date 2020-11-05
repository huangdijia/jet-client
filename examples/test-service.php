<?php
require_once __DIR__ . '/../src/bootstrap.php';

$configs = include __DIR__ . '/config.php';
$host    = JetUtil::arrayGet($configs, 'consul.host', '127.0.0.1');
$port    = JetUtil::arrayGet($configs, 'consul.port', 8500);

$serviceCenter = new JetConsulServiceCenter($host, $port);

JetServiceManager::register('CalculatorService', array(
    // JetServiceManager::TRANSPORTER => new JetCurlHttpTransporter('127.0.0.1', 9502),
    // JetServiceManager::TRANSPORTER => $serviceCenter->getTransporter('CalculatorService'),
    JetServiceManager::SERVICE_CENTER => $serviceCenter,
));
JetServiceManager::register('CalculatorTcpService', array(
    // JetServiceManager::TRANSPORTER => new JetStreamSocketTransporter('127.0.0.1', 9503),
    // JetServiceManager::TRANSPORTER => $serviceCenter->getTransporter('CalculatorTcpService', 'jsonrpc'),
    JetServiceManager::SERVICE_CENTER => $serviceCenter,
));

/**
 * @method mixed add($a, $b)
 * @package
 */
class CalculatorService extends AbstractJetClient
{
    public function __construct()
    {
        parent::__construct(JetClientFactory::create('CalculatorService'));
    }
}

$service = new CalculatorService;
var_dump($service->add(rand(0, 100), rand(0, 100)));

$client = JetClientFactory::create('CalculatorTcpService');
var_dump($client->add(rand(0, 100), rand(0, 100)));

$client = JetClientFactory::create('CalculatorService', new JetCurlHttpTransporter('127.0.0.1', 9502));
var_dump($client->add(rand(0, 100), rand(0, 100)));
