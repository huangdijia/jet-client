# Hyperf jet client for PHP5.3

## Usage

### Require

~~~php
require 'path/jet-client/bootstrap.php';
~~~

or composer install

~~~php
composer require huangdijia/jet-client
~~~

### Register consul

~~~php
Jet::addConsul('http://127.0.0.1:8500', 1);
~~~

### Define a service class

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

### Direct call

~~~php
$client = Jet::create('CalculatorService');
var_dump($client->add(1, 20));
~~~
