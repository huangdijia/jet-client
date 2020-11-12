<?php

require_once __DIR__ . '/../src/bootstrap.php';

$agent = new JetConsulAgent([
    'uri'     => 'http://127.0.0.1:8500',
    'timeout' => 2,
]);

$protocols = array('jsonrpc-http', 'jsonrpc');
$ports     = array(9502, 9503);
$host      = PHP_OS === 'Darwin' ? 'docker.for.mac.host.internal' : 'localhost';

foreach ($protocols as $i => $protocol) {
    // $agent
    $requestBody = [
        'Name'    => 'CalculatorService',
        'ID'      => 'CalculatorService-' . $protocol,
        'Address' => '127.0.0.1',
        'Port'    => $ports[$i],
        'Meta'    => [
            'Protocol' => $protocol,
        ],
    ];

    switch ($protocol) {
        case 'jsonrpc-http':
            $requestBody['Check'] = array(
                'DeregisterCriticalServiceAfter' => '90m',
                'HTTP'                           => "http://{$host}:{$ports[$i]}/",
                'Interval'                       => '1s',
            );
            break;
        case 'jsonrpc':
        case 'jsonrpc-tcp-length-check':
            $requestBody['Check'] = array(
                'DeregisterCriticalServiceAfter' => '90m',
                'TCP'                            => "{$host}:{$ports[$i]}",
                'Interval'                       => '1s',
            );
            break;
    }

    $agent->registerService($requestBody);
}
