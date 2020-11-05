# Hyperf jet client for PHP5.3+

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
composer require huangdijia/jet-client
~~~

## Quickstart

### Register a service

~~~php
JetServiceManager::register('CalculatorService', array(
    // register transporter
    JetServiceManager::TRANSPORTER => new JetCurlHttpTransporter('127.0.0.1', 9502),
    // register service center
    JetServiceManager::SERVICE_CENTER => new JetConsulServiceCenter('127.0.0.1', 8500),
));
~~~

### Register services by service center

~~~php

$consulServiceCenter = new JetConsulServiceCenter($host, $port);
$services            = $consulServiceCenter->getServices();

foreach ($services as $service) {
    JetServiceManager::register($service, array(
        JetServiceManager::SERVICE_CENTER => $consulServiceCenter,
    ));
}
~~~

## Call RPC method

### Call by ClientFactory

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
    public function __construct($service = 'CalculatorService', $transporter = null, $packer = null, $dataFormatter = null, $pathGenerator = null)
    {
        $serviceCenter = new JetConsulServiceCenter('127.0.0.1', 8500);
        $transporter   = $serviceCenter->getTransporter($service);

        // or
        $transporter = new JetCurlHttpTransporter('127.0.0.1', 9502);

        parent::__construct($service, $transporter, $packer, $dataFormatter, $pathGenerator);
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
