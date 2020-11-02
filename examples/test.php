<?php
require_once __DIR__ . '/../src/bootstrap.php';

$consul = new JetConsulporter('127.0.0.1', 8500);

JetServiceManager::register('CalculatorService', array(
    // JetServiceManager::TRANSPORTER => new JetCurlHttpTransporter('127.0.0.1', 9502),
    JetServiceManager::TRANSPORTER => $consul->getTransporter('CalculatorService'),
    // JetServiceManager::CONSULPORTER => $consul,
));
JetServiceManager::register('TcpService', array(
    // JetServiceManager::TRANSPORTER => new JetStreamSocketTransporter('127.0.0.1', 9503),
    JetServiceManager::TRANSPORTER => $consul->getTransporter('TcpService'),
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
var_dump($service->add(3, 10));

$client = JetClientFactory::create('TcpService');
var_dump($client->add(1, 20));

$client = JetClientFactory::create('CalculatorService', new JetCurlHttpTransporter('127.0.0.1', 9502));
var_dump($client->add(5, 20));
