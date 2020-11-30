<?php
require_once __DIR__ . '/../src/bootstrap.php';

$configFile = is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.php.dist';
$configs    = include $configFile;

$uri             = JetUtil::arrayGet($configs, 'consul.uri', 'http://127.0.0.1:8500');
$jsonrpcHttpHost = JetUtil::arrayGet($configs, 'jsonrpc.http.host', '127.0.0.1');
$jsonrpcHttpPort = JetUtil::arrayGet($configs, 'jsonrpc.http.port', 9502);
$jsonrpcTcpHost  = JetUtil::arrayGet($configs, 'jsonrpc.tcp.host', '127.0.0.1');
$jsonrpcTcpPort  = JetUtil::arrayGet($configs, 'jsonrpc.tcp.port', 9503);

echo sprintf("CONSUL_URI: %s\n", $uri);

$service  = 'CalculatorService';
$registry = new JetConsulRegistry(array('uri' => $uri));

JetServiceManager::registerDefaultRegistry($registry, true);

// JetServiceManager::register($service, array(
//     JetServiceManager::TRANSPORTER => new JetCurlHttpTransporter($jsonrpcHttpHost, $jsonrpcHttpPort),
//     JetServiceManager::TRANSPORTER => $registry->getTransporter($service),
//     JetServiceManager::REGISTRY => $registry,
// ));

echo "Create with http transporter\n";
$client = JetClientFactory::create($service, new JetCurlHttpTransporter($jsonrpcHttpHost, $jsonrpcHttpPort));
var_dump($client->add(rand(0, 100), rand(0, 100)));

echo "Create with tcp transporter\n";
$client = JetClientFactory::create($service, new JetStreamSocketTransporter($jsonrpcTcpHost, $jsonrpcTcpPort));
var_dump($client->add(rand(0, 100), rand(0, 100)));

// echo "Create with jsonrpc-http protocol\n";
// $client = JetClientFactory::create($service, 'jsonrpc-http');
// var_dump($client->add(rand(0, 100), rand(0, 100)));

// echo "Create with jsonrpc protocol\n";
// $client = JetClientFactory::create($service, 'jsonrpc');
// var_dump($client->add(rand(0, 100), rand(0, 100)));

echo "Create with facade\n";
/**
 * @method static int add(int $a, int $b)
 */
class Calculator extends JetFacade
{
    protected static function getFacadeAccessor()
    {
        global $jsonrpcHttpHost, $jsonrpcHttpPort;

        return JetClientFactory::create('CalculatorService', new JetCurlHttpTransporter($jsonrpcHttpHost, $jsonrpcHttpPort));
        // return 'CalculatorService';
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
        global $jsonrpcHttpHost, $jsonrpcHttpPort;

        $transporter = new JetCurlHttpTransporter($jsonrpcHttpHost, $jsonrpcHttpPort);

        parent::__construct($service, $transporter, $packer, $dataFormatter, $pathGenerator);
    }
}

$service = new CalculatorService;
var_dump($service->add(rand(0, 100), rand(0, 100)));
