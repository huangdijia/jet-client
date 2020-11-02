<?php
require_once __DIR__ . '/../src/bootstrap.php';

$configs = include __DIR__ . '/config.php';
$catalog = new JetConsulCatalog(array(
    'uri' => sprintf('http://%s:%s', $configs['consul']['host'], $configs['consul']['port']),
));
$services = $catalog->services();

var_dump($services);
