<?php

$baseDir = __DIR__;

$classmap = array(
    'JetDataFormatterInterface'  => $baseDir . '/Contract/JetDataFormatterInterface.php',
    'JetPackerInterface'         => $baseDir . '/Contract/JetPackerInterface.php',
    'JetPathGeneratorInterface'  => $baseDir . '/Contract/JetPathGeneratorInterface.php',
    'JetLoadBalancerInterface'   => $baseDir . '/Contract/JetLoadBalancerInterface.php',
    'JetTransporterInterface'    => $baseDir . '/Contract/JetTransporterInterface.php',

    'JetConsulClient'            => $baseDir . '/Consul/JetConsulClient.php',
    'JetConsulHealth'            => $baseDir . '/Consul/JetConsulHealth.php',

    'JetClientException'         => $baseDir . '/Exception/JetClientException.php',
    'JetServerException'         => $baseDir . '/Exception/JetServerException.php',
    'JetRecvFailedException'     => $baseDir . '/Exception/JetRecvFailedException.php',

    'Jet'                        => $baseDir . '/Jet.php',
    'AbstractJetClient'          => $baseDir . '/AbstractJetClient.php',
    'JetClient'                  => $baseDir . '/JetClient.php',
    'JetUtil'                    => $baseDir . '/JetUtil.php',

    'AbstractJetLoadBalancer'    => $baseDir . '/LoadBalancer/AbstractJetLoadBalancer.php',
    'JetRoundRobinLoadBalancer'  => $baseDir . '/LoadBalancer/JetRoundRobinLoadBalancer.php',
    'JetRandomLoadBalancer'      => $baseDir . '/LoadBalancer/JetRandomLoadBalancer.php',
    'JetLoadBalancerNode'        => $baseDir . '/LoadBalancer/JetLoadBalancerNode.php',

    'AbstractJetTransporter'     => $baseDir . '/Transporter/AbstractJetTransporter.php',
    'JetCurlHttpTransporter'     => $baseDir . '/Transporter/JetCurlHttpTransporter.php',
    'JetStreamSocketTransporter' => $baseDir . '/Transporter/JetStreamSocketTransporter.php',
    'JetJsonEofPacker'           => $baseDir . '/Packer/JetJsonEofPacker.php',
    'JetJsonLengthPacker'        => $baseDir . '/Packer/JetJsonLengthPacker.php',

    'JetDataFormatter'           => $baseDir . '/DataFormatter/JetDataFormatter.php',

    'JetPathGenerator'           => $baseDir . '/PathGenerator/JetPathGenerator.php',
);

spl_autoload_register(function ($class) use ($classmap) {
    if (isset($classmap[$class]) && is_file($classmap[$class])) {
        require_once $classmap[$class];
    }
});
