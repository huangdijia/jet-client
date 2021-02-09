# Hyperf jet client for PHP5.3+

[![Latest Test](https://github.com/huangdijia/jet-client/workflows/tests-1.x/badge.svg)](https://github.com/huangdijia/jet-client/actions)
[![Latest Stable Version](https://poser.pugx.org/huangdijia/jet-client/version.png)](https://packagist.org/packages/huangdijia/jet-client)
[![Total Downloads](https://poser.pugx.org/huangdijia/jet-client/d/total.png)](https://packagist.org/packages/huangdijia/jet-client)
[![GitHub license](https://img.shields.io/github/license/huangdijia/jet-client)](https://github.com/huangdijia/jet-client)

## Installation

### Require

~~~php
require 'path/jet-client/bootstrap.php';
~~~

### Composer

~~~php
composer require "huangdijia/jet-client:^1.0"
~~~

## Quickstart

### Register with metadata

~~~php
JetServiceManager::register('CalculatorService', [
    // register with transporter
    JetServiceManager::TRANSPORTER => new JetCurlHttpTransporter('127.0.0.1', 9502),
    // or register with registry
    JetServiceManager::REGISTRY => new JetConsulRegistry(array('uri' => 'http://127.0.0.1:8500')),
]);
~~~

### Auto register services by registry

~~~php
$registry = new JetConsulRegistry(array('uri' => 'http://127.0.0.1:8500'));
$registry->register('CalculatorService'); // register a service
$registry->register(['CalculatorService', 'AnotherService']); // register some services
$registry->register(); // register all service
~~~

### Register default registry

~~~php
JetRegistryManager::register(JetRegistryManager::DEFAULT_REGISTRY, new JetConsulRegistry(array('uri' => 'http://127.0.0.1:8500')));
~~~

## Call RPC method

### Call by JetClientFactory

~~~php
$client = JetClientFactory::create('CalculatorService');
var_dump($client->add(1, 20));
~~~

### Call by custom client

~~~php
/**
 * @method int add(int $a, int $b)
 */
class CalculatorService extends JetClient
{
    public function __construct($service = 'CalculatorService', $transporter = null, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        // Custom transporter
        $transporter = new JetCurlHttpTransporter('127.0.0.1', 9502);

        // Or get tranporter by registry
        $registry    = new JetConsulRegistry(array('uri' => 'http://127.0.0.1:8500'));
        $transporter = $registry->getTransporter($service);

        parent::__construct($service, $transporter, $packer, $dataFormatter, $pathGenerator, $tries);
    }
}

$service = new CalculatorService;
var_dump($service->add(3, 10));
~~~

### Call by custom facade

~~~php
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
~~~
