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

### Register service

~~~php
JetServiceManager::register('CalculatorService', array(
    // register by transporter
    JetServiceManager::TRANSPORTER => new JetCurlHttpTransporter('127.0.0.1', 9502),
    // or register by service center
    JetServiceManager::SERVICE_CENTER => new JetConsulServiceCenter('127.0.0.1', 8500),
));
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
 * @method mixed add($a, $b)
 */
class CalculatorService extends AbstractJetClient
{
    public function __construct()
    {
        parent::__construct(Jet::create('CalculatorService'));
    }
}

$service = new CalculatorService;
var_dump($service->add(3, 10));
~~~
