<?php
require_once __DIR__ . '/../src/bootstrap.php';

$configs = include __DIR__ . '/config.php';
$host    = JetUtil::arrayGet($configs, 'consul.host', '127.0.0.1');
$port    = JetUtil::arrayGet($configs, 'consul.port', 8500);

$catalog = new JetConsulCatalog(array(
    'uri' => sprintf('http://%s:%s', $host, $port),
));
$services = $catalog->services()->json();
var_dump($services);

$registry = new JetConsulRegistry($host, $port);
$services = $registry->getServices();
var_dump($services);

foreach ($services as $service) {
    JetServiceManager::register($service, array(
        JetServiceManager::REGISTRY => $registry,
    ));
}

foreach ($services as $service) {
    var_dump(JetServiceManager::get($service));
}
