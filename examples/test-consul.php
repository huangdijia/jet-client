<?php
require_once __DIR__ . '/../src/bootstrap.php';

$configs = include __DIR__ . '/config.php';
$host    = JetUtil::arrayGet($configs, 'consul.host', '127.0.0.1');
$port    = JetUtil::arrayGet($configs, 'consul.port', 8500);

$catalog = new JetConsulCatalog(array(
    'uri' => sprintf('http://%s:%s', $host, $port),
));
$services = $catalog->services();
var_dump($services);

$consulServiceCenter = new JetConsulServiceCenter($host, $port);
$services            = $consulServiceCenter->getServices();
var_dump($services);

foreach ($services as $service) {
    JetServiceManager::register($service, array(
        JetServiceManager::SERVICE_CENTER => $consulServiceCenter,
    ));
}

$client = JetClientFactory::create('CalculatorService');
var_dump($client->add(rand(1, 100), rand(1, 100)));

$client = JetClientFactory::create('TcpService');
var_dump($client->add(rand(1, 100), rand(1, 100)));
