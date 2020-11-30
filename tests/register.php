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
use Huangdijia\Jet\Consul\Agent;

$agent = new Agent(function () {
    return new Client([
        'base_uri' => 'http://127.0.0.1:8500',
        'timeout' => 2,
    ]);
});

$protocols = ['jsonrpc-http', 'jsonrpc'];
$ports = [9502, 9503];
$host = PHP_OS === 'Darwin' ? 'docker.for.mac.host.internal' : 'localhost';
foreach ($protocols as $i => $protocol) {
    // $agent
    $requestBody = [
        'Name' => 'CalculatorService',
        'ID' => 'CalculatorService-' . $protocol,
        'Address' => '127.0.0.1',
        'Port' => $ports[$i],
        'Meta' => [
            'Protocol' => $protocol,
        ],
    ];

    switch ($protocol) {
        case 'jsonrpc-http':
            $requestBody['Check'] = [
                'DeregisterCriticalServiceAfter' => '90m',
                'HTTP' => "http://{$host}:{$ports[$i]}/",
                'Interval' => '1s',
            ];
            break;
        case 'jsonrpc':
        case 'jsonrpc-tcp-length-check':
            $requestBody['Check'] = [
                'DeregisterCriticalServiceAfter' => '90m',
                'TCP' => "{$host}:{$ports[$i]}",
                'Interval' => '1s',
            ];
            break;
    }

    try {
        $agent->registerService($requestBody);

        echo "{$requestBody['ID']} registered.\n";
    } catch (Throwable $e) {
        echo "Registered failed, error:{$e->getMessage()}\n";
    }
}
