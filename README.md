# Hyperf jet client

[![Latest Test](https://github.com/huangdijia/jet-client/workflows/tests/badge.svg)](https://github.com/huangdijia/jet-client/actions)
[![Latest Stable Version](https://poser.pugx.org/huangdijia/jet-client/version.png)](https://packagist.org/packages/huangdijia/jet-client)
[![Total Downloads](https://poser.pugx.org/huangdijia/jet-client/d/total.png)](https://packagist.org/packages/huangdijia/jet-client)
[![GitHub license](https://img.shields.io/github/license/huangdijia/jet-client)](https://github.com/huangdijia/jet-client)

New repository: https://github.com/friendsofhyperf/jet

## Installation

### Composer

~~~php
composer require "huangdijia/jet-client:^2.0"
~~~

## Quickstart

### Register with metadata

~~~php
use Huangdijia\Jet\ServiceManager;
use Huangdijia\Jet\Registry\ConsulRegistry;
use Huangdijia\Jet\Transporter\GuzzleHttpTransporter;

ServiceManager::register('CalculatorService', [
    // register with transporter
    ServiceManager::TRANSPORTER => new GuzzleHttpTransporter('127.0.0.1', 9502),
    // or register with registry
    ServiceManager::REGISTRY => new ConsulRegistry(['uri' => 'http://127.0.0.1:8500']),
]);
~~~

### Auto register services by registry

~~~php
use Huangdijia\Jet\ServiceManager;
use Huangdijia\Jet\Registry\ConsulRegistry;

$registry = new ConsulRegistry(['uri' => 'http://127.0.0.1:8500']);
$registry->register('CalculatorService'); // register a service
$registry->register(['CalculatorService', 'CalculatorService2']); // register some services
$registry->register(); // register all service
~~~

### Register default registry

~~~php
use Huangdijia\Jet\RegistryManager;
use Huangdijia\Jet\Registry\ConsulRegistry;

RegistryManager::register(RegistryManager::DEFAULT, new new ConsulRegistry(['uri' => $uri, 'timeout' => 1]));
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
use Huangdijia\Jet\Transporter\GuzzleHttpTransporter;
use Huangdijia\Jet\Registry\ConsulRegistry;

/**
 * @method int add(int $a, int $b)
 */
class CalculatorService extends Client
{
    public function __construct($service = 'CalculatorService', $transporter = null, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        // Custom transporter
        $transporter = new GuzzleHttpTransporter('127.0.0.1', 9502);

        // Or get tranporter by registry
        $registry    = new ConsulRegistry(['uri' => 'http://127.0.0.1:8500']);
        $transporter = $registry->getTransporter($service);

        parent::__construct($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
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
