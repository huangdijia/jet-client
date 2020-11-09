# Hyperf jet client for PHP5.3+

[![Latest Stable Version](https://poser.pugx.org/huangdijia/jet-client/version.png)](https://packagist.org/packages/huangdijia/jet-client)
[![Total Downloads](https://poser.pugx.org/huangdijia/jet-client/d/total.png)](https://packagist.org/packages/huangdijia/jet-client)
[![GitHub license](https://img.shields.io/github/license/huangdijia/jet-client)](https://github.com/huangdijia/jet-client)

## Installation

### Composer

~~~php
composer require huangdijia/jet-client
~~~

## Quickstart

### Register a service

~~~php
use Huangdijia\Jet\ServiceManager;
use Huangdijia\Jet\Registry\ConsulRegistry;
use Huangdijia\Jet\Transporter\CurlHttpTransporter;

ServiceManager::register('CalculatorService', [
    // register transporter
    ServiceManager::TRANSPORTER => new CurlHttpTransporter('127.0.0.1', 9502),
    // register service center
    ServiceManager::REGISTRY => new ConsulRegistry('127.0.0.1', 8500),
]);
~~~

### Register services by service center

~~~php
use Huangdijia\Jet\ServiceManager;
use Huangdijia\Jet\Registry\ConsulRegistry;

$registry = new ConsulRegistry($host, $port);
$services = $registry->getServices();

foreach ($services as $service) {
    ServiceManager::register($service, [
        ServiceManager::REGISTRY => $registry,
    ]);
}
~~~

## Call RPC method

### Call by ClientFactory

~~~php
use Huangdijia\Jet\ClientFactory;

$client = ClientFactory::create('CalculatorService');
var_dump($client->add(1, 20));
~~~

### Call by custom client

~~~php
use Huangdijia\Jet\Client;
use Huangdijia\Jet\Transporter\CurlHttpTransporter;
use Huangdijia\Jet\Registry\ConsulRegistry;

/**
 * @method int add(int $a, int $b)
 */
class CalculatorService extends Client
{
    public function __construct($service = 'CalculatorService', $transporter = null, $packer = null, $dataFormatter = null, $pathGenerator = null)
    {
        $registry    = new ConsulRegistry('127.0.0.1', 8500);
        $transporter = $registry->getTransporter($service);

        // or
        $transporter = new CurlHttpTransporter('127.0.0.1', 9502);

        parent::__construct($service, $transporter, $packer, $dataFormatter, $pathGenerator);
    }
}

$service = new CalculatorService;
var_dump($service->add(3, 10));
~~~

### Call by custom facade

~~~php
use Huangdijia\Jet\Facade;
use Huangdijia\Jet\ClientFactory;

/**
 * @method static int add(int $a, int $b)
 */
class Calculator extends Facade
{
    protected static function getFacadeAccessor()
    {
        // return ClientFactory::create('CalculatorService');
        return 'CalculatorService';
    }
}

var_dump(Calculator::add(rand(0, 100), rand(0, 100)));
~~~
