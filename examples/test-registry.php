<?php

declare(strict_types=1);
/**
 * This file is part of Jet-Client.
 *
 * @link     https://github.com/huangdijia/jet-client
 * @document https://github.com/huangdijia/jet-client/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/jet-client/blob/main/LICENSE
 */
require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use Huangdijia\Jet\Consul\Catalog;
use Huangdijia\Jet\Registry\ConsulRegistry;
use Huangdijia\Jet\ServiceManager;

$configs = include __DIR__ . '/config.php';
$host = array_get($configs, 'consul.host', '127.0.0.1');
$port = array_get($configs, 'consul.port', 8500);

$catalog = new Catalog(function () use ($host, $port) {
    return new Client([
        'base_uri' => sprintf('http://%s:%s', $host, $port),
    ]);
});
$services = $catalog->services()->json();
var_dump($services);

$registry = new ConsulRegistry($host, $port);
$services = $registry->getServices();
var_dump($services);

foreach ($services as $service) {
    ServiceManager::register($service, [
        ServiceManager::REGISTRY => $registry,
    ]);
}

foreach ($services as $service) {
    var_dump(ServiceManager::get($service));
}
