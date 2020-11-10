<?php

namespace Huangdijia\Jet\Registry;

use GuzzleHttp\Client;
use Huangdijia\Jet\Consul\Catalog;
use Huangdijia\Jet\Consul\Health;
use Huangdijia\Jet\Contract\LoadBalancerInterface;
use Huangdijia\Jet\Contract\RegistryInterface;
use Huangdijia\Jet\LoadBalancer\Node;
use Huangdijia\Jet\LoadBalancer\RoundRobin;
use Huangdijia\Jet\Transporter\GuzzleHttpTransporter;
use Huangdijia\Jet\Transporter\StreamSocketTransporter;
use RuntimeException;

class ConsulRegistry implements RegistryInterface
{
    /**
     * @var string
     */
    protected $host;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var int
     */
    protected $timeout;
    /**
     * @var LoadBalancerInterface|null
     */
    protected $loadBalancer;

    /**
     * @param string $host
     * @param int $port
     * @param int $timeout
     */
    public function __construct(string $host = '127.0.0.1', int $port = 8500, int $timeout = 1)
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
    }

    public function setLoadBalancer(?LoadBalancerInterface $loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;
    }

    public function getLoadBalancer()
    {
        if (!$this->loadBalancer) {
            $this->loadBalancer = new RoundRobin();
            $this->loadBalancer->setNodes([
                new Node('', 0, 1, [
                    'uri'     => sprintf('http://%s:%s', $this->host, $this->port),
                    'timeout' => $this->timeout,
                ]),
            ]);
        }

        return $this->loadBalancer;
    }

    public function getServices()
    {
        $loadBalancer = $this->getLoadBalancer();

        return retry(count($loadBalancer->getNodes()), function () use ($loadBalancer) {
            $catalog = new Catalog(function () use ($loadBalancer) {
                /** @var LoadBalancerInterface $loadBalancer */
                $node    = $loadBalancer->select();
                $options = $node->options;

                $options['base_uri'] = $options['base_uri'] ?? $options['uri'] ?? sprintf('http://%s:%s', $node->host, $node->port);
                $options['timeout']  = $options['timeout'] ?? 1;

                return new Client($options);
            });

            return with($catalog->services()->json(), function ($services) {
                return array_keys($services);
            });
        });
    }

    public function getServiceNodes(string $service, ?string $protocol = null)
    {
        $loadBalancer = $this->getLoadBalancer();

        return retry(count($loadBalancer->getNodes()), function () use ($loadBalancer, $service, $protocol) {
            $health = new Health(function () use ($loadBalancer) {
                $node    = $loadBalancer->select();
                $options = $node->options ?? [];

                $options['base_uri'] = $options['base_uri'] ?? $options['uri'] ?? sprintf('http://%s:%s', $node->host, $node->port);
                $options['timeout']  = $options['timeout'] ?? 1;

                return new Client($options);
            });

            return with($health->service($service)->json(), function ($serviceNodes) use ($protocol) {
                /** @var array $serviceNodes */
                $nodes = [];

                foreach ($serviceNodes as $node) {
                    if (array_get($node, 'Checks.1.Status') != 'passing') {
                        continue;
                    }

                    if (!is_null($protocol) && $protocol != array_get($node, 'Service.Meta.Protocol')) {
                        continue;
                    }

                    $nodes[] = new Node(
                        array_get($node, 'Service.Address'),
                        (int) array_get($node, 'Service.Port'),
                        1,
                        [
                            'type'     => array_get($node, 'Checks.1.Type'),
                            'protocol' => array_get($node, 'Service.Meta.Protocol'),
                        ]
                    );
                }

                return $nodes;
            });
        });
    }

    public function getTransporter(string $service, ?string $protocol = null)
    {
        $nodes = $this->getServiceNodes($service, $protocol);

        throw_if(count($nodes) <= 0, new RuntimeException('Service nodes not found!'));

        $transporter     = null;
        $serviceBalancer = new RoundRobin();
        $serviceBalancer->setNodes($nodes);

        if (is_null($transporter)) {
            $node = $serviceBalancer->select();

            if ($node->options['type'] == 'tcp') {
                $transporter = new StreamSocketTransporter($node->host, $node->port);
            } else {
                // $transporter = new GuzzleHttpTransporter($node->host, $node->port);
                $transporter = new GuzzleHttpTransporter($node->host, $node->port);
            }
        }

        if (count($nodes) > 1) {
            $transporter->setLoadBalancer($serviceBalancer);
        }

        return $transporter;
    }
}
