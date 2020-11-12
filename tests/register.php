<?php

require_once __DIR__ . '/../src/bootstrap.php';

$configFile = is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.php.dist';
$configs    = include $configFile;

$host = JetUtil::arrayGet($configs, 'consul.host', '127.0.0.1');
$port = JetUtil::arrayGet($configs, 'consul.port', 8500);

var_dump($host, $port);

$agent = new JetConsulAgent(array(
    'uri'     => sprintf('http://%s:%s', $host, $port),
    'timeout' => 2,
));

$protocols = array('jsonrpc-http', 'jsonrpc');
$ports     = array(9502, 9503);
$host      = PHP_OS === 'Darwin' ? 'docker.for.mac.host.internal' : 'localhost';

foreach ($protocols as $i => $protocol) {
    echo "Registering {$protocol} ...\n";

    // $agent
    $requestBody = array(
        'Name'    => 'CalculatorService',
        'ID'      => 'CalculatorService-' . $protocol,
        'Address' => '127.0.0.1',
        'Port'    => $ports[$i],
        'Meta'    => array(
            'Protocol' => $protocol,
        ),
    );

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

    // var_dump($requestBody);
    $response = $agent->registerService($requestBody)->throwIf();
    var_dump($response->body());
}
