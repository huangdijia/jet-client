<?php
require_once __DIR__ . '/../src/bootstrap.php';

/**
 * @method mixed add($a, $b)
 * @package 
 */
class CalculatorService extends AbstractJetClient
{
    public function __construct()
    {
        Jet::addConsul('http://127.0.0.1:8500', 1);

        parent::__construct(Jet::create('CalculatorService'));
    }
}

$service = new CalculatorService;
var_dump($service->add(3, 10));

$client = Jet::create('TcpService');
var_dump($client->add(1, 20));

$client = Jet::create('CalculatorService', new JetCurlHttpTransporter('127.0.0.1', 9502));
var_dump($client->add(5, 20));