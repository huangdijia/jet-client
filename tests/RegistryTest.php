<?php
require_once __DIR__ . '/../src/bootstrap.php';

$configFile = is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.php.dist';
$configs    = include $configFile;

$host = JetUtil::arrayGet($configs, 'consul.host', '127.0.0.1');
$port = JetUtil::arrayGet($configs, 'consul.port', 8500);

echo sprintf("CONSUL_URI: http://%s:%s\n", $host, $port);

$catalog = new JetConsulCatalog(array(
    'uri' => sprintf('http://%s:%s', $host, $port),
));
$services = $catalog->services()->json();
var_dump($services);

$registry = new JetConsulRegistry($host, $port);
$services = $registry->getServices();
var_dump($services);

$nodes = $registry->getServiceNodes($service  = 'CalculatorService');
var_dump($nodes);
