<?php

$baseDir = __DIR__;
$classMap = array(
    'JetConsulporterInterface' => $baseDir . '/src/Contract/JetConsulporterInterface.php',
    'JetDataFormatterInterface' => $baseDir . '/src/Contract/JetDataFormatterInterface.php',
    'JetPackerInterface' => $baseDir . '/src/Contract/JetPackerInterface.php',
    'JetPathGeneratorInterface' => $baseDir . '/src/Contract/JetPathGeneratorInterface.php',
    'JetLoadBalancerInterface' => $baseDir . '/src/Contract/JetLoadBalancerInterface.php',
    'JetTransporterInterface' => $baseDir . '/src/Contract/JetTransporterInterface.php',
    'JetConsulporter' => $baseDir . '/src/Consulporter/JetConsulporter.php',
    'AbstractJetClient' => $baseDir . '/src/AbstractJetClient.php',
    'JetCurlHttpTransporter' => $baseDir . '/src/Transporter/JetCurlHttpTransporter.php',
    'JetStreamSocketTransporter' => $baseDir . '/src/Transporter/JetStreamSocketTransporter.php',
    'AbstractJetTransporter' => $baseDir . '/src/Transporter/AbstractJetTransporter.php',
    'JetRoundRobinLoadBalancer' => $baseDir . '/src/LoadBalancer/JetRoundRobinLoadBalancer.php',
    'AbstractJetLoadBalancer' => $baseDir . '/src/LoadBalancer/AbstractJetLoadBalancer.php',
    'JetRandomLoadBalancer' => $baseDir . '/src/LoadBalancer/JetRandomLoadBalancer.php',
    'JetLoadBalancerNode' => $baseDir . '/src/LoadBalancer/JetLoadBalancerNode.php',
    'JetClient' => $baseDir . '/src/JetClient.php',
    'bootstrap' => $baseDir . '/src/bootstrap.php',
    'JetPathGenerator' => $baseDir . '/src/PathGenerator/JetPathGenerator.php',
    'Jet' => $baseDir . '/src/Jet.php',
    'JetConsulClient' => $baseDir . '/src/Consul/JetConsulClient.php',
    'JetConsulHealth' => $baseDir . '/src/Consul/JetConsulHealth.php',
    'JetUtil' => $baseDir . '/src/JetUtil.php',
    'JetServerException' => $baseDir . '/src/Exception/JetServerException.php',
    'JetClientException' => $baseDir . '/src/Exception/JetClientException.php',
    'JetRecvFailedException' => $baseDir . '/src/Exception/JetRecvFailedException.php',
    'JetDataFormatter' => $baseDir . '/src/DataFormatter/JetDataFormatter.php',
    'JetJsonEofPacker' => $baseDir . '/src/Packer/JetJsonEofPacker.php',
    'JetJsonLengthPacker' => $baseDir . '/src/Packer/JetJsonLengthPacker.php',
);

spl_autoload_register(function ($class) use ($classMap) {
    if (isset($classMap[$class]) && is_file($classMap[$class])) {
        require_once $classMap[$class];
    }
});
