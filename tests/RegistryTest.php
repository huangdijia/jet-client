<?php
require_once __DIR__ . '/../src/bootstrap.php';

$configFile = is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.php.dist';
$configs    = include $configFile;

$uri = JetUtil::arrayGet($configs, 'consul.uri', 'http://127.0.0.1:8500');

echo sprintf("CONSUL_URI: %s\n", $uri);

$catalog = new JetConsulCatalog(array(
    'uri' => $uri,
));

echo "Test get services by JetConsulCatalog\n";
$services = $catalog->services()->json();
var_dump($services);

echo "Test get services by JetConsulRegistry\n";
$registry = new JetConsulRegistry(array('uri' => $uri));
$services = $registry->getServices();
var_dump($services);

echo "Test get service nodes\n";
$nodes = $registry->getServiceNodes($service  = 'CalculatorService');
var_dump($nodes);

echo "Test JetRegistryManager::register()\n";
JetRegistryManager::register(JetRegistryManager::DEFAULT_REGISTRY, $registry, true);
var_dump(JetRegistryManager::get(JetRegistryManager::DEFAULT_REGISTRY) instanceof JetRegistryInterface);