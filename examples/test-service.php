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
JetServiceManager::register('CalculatorService:tcp', array(
    // JetServiceManager::TRANSPORTER => new JetStreamSocketTransporter('127.0.0.1', 9503),
    // JetServiceManager::TRANSPORTER => $serviceCenter->getTransporter('CalculatorTcpService', 'jsonrpc'),
    JetServiceManager::SERVICE_CENTER => $serviceCenter,
));

/**
 * @method static int add(int $a, int $b)
 */
class CalculatorFacade extends JetFacade
{
    protected static function getFacadeAccessor()
    {
        // return JetClientFactory::create('CalculatorService');
        return 'CalculatorService';
    }
}

var_dump(CalculatorFacade::add(rand(0, 100), rand(0, 100)));

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

$client = JetClientFactory::create('CalculatorService:tcp', 'jsonrpc');
var_dump($client->add(rand(0, 100), rand(0, 100)));

$client = JetClientFactory::create('CalculatorService', new JetCurlHttpTransporter('127.0.0.1', 9502));
var_dump($client->add(rand(0, 100), rand(0, 100)));
