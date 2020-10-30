# Hyperf jet client for PHP5.3

## Installation

### Require

~~~php
require 'path/jet-client/bootstrap.php';
~~~

### Composer

~~~php
composer require huangdijia/jet-client
~~~

## Usage

### Simple

~~~php
$client = Jet::create('CalculatorService', new JetCurlHttpTransporter('127.0.0.1', 9502));
var_dump($client->add(1, 20));
~~~

### Consul

~~~php
Jet::addConsul('http://127.0.0.1:8500', 1);

$client = Jet::create('CalculatorService');
var_dump($client->add(1, 20));
~~~

### Custom client

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
