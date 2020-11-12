<?php
require_once __DIR__ . '/../src/bootstrap.php';

$configFile = is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.php.dist';
$configs    = include $configFile;

$host = JetUtil::arrayGet($configs, 'consul.host', '127.0.0.1');
$port = JetUtil::arrayGet($configs, 'consul.port', 8500);

echo sprintf("CONSUL_URI: http://%s:%s\n", $host, $port);

$service  = 'CalculatorService';
$registry = new JetConsulRegistry($host, $port);

JetServiceManager::registerDefaultRegistry($registry);

// JetServiceManager::register($service, array(
// JetServiceManager::TRANSPORTER => new JetCurlHttpTransporter('127.0.0.1', 9502),
// JetServiceManager::TRANSPORTER => $registry->getTransporter($service),
// JetServiceManager::REGISTRY => $registry,
// ));

echo "Create with http transporter\n";
$client = JetClientFactory::create($service, new JetCurlHttpTransporter('127.0.0.1', 9502));
var_dump($client->add(rand(0, 100), rand(0, 100)));

echo "Create with tcp transporter\n";
$client = JetClientFactory::create($service, new JetStreamSocketTransporter('127.0.0.1', 9503));
var_dump($client->add(rand(0, 100), rand(0, 100)));

echo "Create with jsonrpc-http protocol\n";
$client = JetClientFactory::create($service, 'jsonrpc-http');
var_dump($client->add(rand(0, 100), rand(0, 100)));

// echo "Create with jsonrpc protocol\n";
// $client = JetClientFactory::create($service, 'jsonrpc');
// var_dump($client->add(rand(0, 100), rand(0, 100)));

echo "Create with face\n";
/**
 * @method static int add(int $a, int $b)
 */
class Calculator extends JetFacade
{
    protected static function getFacadeAccessor()
    {
        // return JetClientFactory::create('CalculatorService');
        return 'CalculatorService';
    }
}

var_dump(Calculator::add(rand(0, 100), rand(0, 100)));

echo "Create with custom client\n";
/**
 * @method int add(int$a, int$b)
 * @package
 */
class CalculatorService extends JetClient
{
    public function __construct($service = 'CalculatorService', $transporter = null, $packer = null, $dataFormatter = null, $pathGenerator = null)
    {
        $transporter = new JetCurlHttpTransporter('127.0.0.1', 9502);

        parent::__construct($service, $transporter, $packer, $dataFormatter, $pathGenerator);
    }
}

$service = new CalculatorService;
var_dump($service->add(rand(0, 100), rand(0, 100)));
