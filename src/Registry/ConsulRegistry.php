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
namespace Huangdijia\Jet\Registry;

use GuzzleHttp\Client;
use Huangdijia\Jet\Consul\Catalog;
use Huangdijia\Jet\Consul\Health;
use Huangdijia\Jet\Contract\LoadBalancerInterface;
use Huangdijia\Jet\Contract\RegistryInterface;
use Huangdijia\Jet\LoadBalancer\Node;
use Huangdijia\Jet\LoadBalancer\RoundRobin;
use Huangdijia\Jet\ServiceManager;
use Huangdijia\Jet\Transporter\GuzzleHttpTransporter;
use Huangdijia\Jet\Transporter\StreamSocketTransporter;
use RuntimeException;

class ConsulRegistry implements RegistryInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var null|LoadBalancerInterface
     */
    protected $loadBalancer;

    /**
     * @param array $options ['uri' => 'http://127.0.0.1:8500', 'timeout' => 1]
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'uri' => 'http://127.0.0.1:8500',
            'timeout' => 1,
        ], $options);
    }

    public function setLoadBalancer(?LoadBalancerInterface $loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;
    }

    public function getLoadBalancer()
    {
        if (! $this->loadBalancer) {
            $this->loadBalancer = new RoundRobin();
            $this->loadBalancer->setNodes([
                new Node('', 0, 1, $this->options),
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
                $node = $loadBalancer->select();
                $options = [];

                $options['base_uri'] = $node->options['uri'];
                $options['timeout'] = $node->options['timeout'] ?? 1;

                if (isset($node->options['token'])) {
                    $options['headers'] = [
                        'X-Consul-Token' => $node->options['token'],
                    ];
                }

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
                $node = $loadBalancer->select();
                $options = $node->options ?? [];

                $options['base_uri'] = $options['base_uri'] ?? sprintf('http://%s:%s', $node->host, $node->port);
                $options['timeout'] = $options['timeout'] ?? 1;

                return new Client($options);
            });

            return with($health->service($service)->json(), function ($serviceNodes) use ($protocol) {
                /** @var array $serviceNodes */
                $nodes = [];

                foreach ($serviceNodes as $node) {
                    if (array_get($node, 'Checks.1.Status') != 'passing') {
                        continue;
                    }

                    if (! is_null($protocol) && $protocol != array_get($node, 'Service.Meta.Protocol')) {
                        continue;
                    }

                    $nodes[] = new Node(
                        array_get($node, 'Service.Address'),
                        (int) array_get($node, 'Service.Port'),
                        1,
                        [
                            'type' => array_get($node, 'Checks.1.Type'),
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

        $serviceBalancer = new RoundRobin($nodes);
        $node = $serviceBalancer->select();

        if ($node->options['type'] == 'tcp') {
            $transporter = new StreamSocketTransporter($node->host, $node->port);
            $serviceBalancer->setNodes(array_filter($nodes, function ($node) {
                return $node->options['type'] == 'tcp';
            }));
        } else {
            $transporter = new GuzzleHttpTransporter($node->host, $node->port);
            $serviceBalancer->setNodes(array_filter($nodes, function ($node) {
                return $node->options['type'] == 'http';
            }));
        }

        if (count($nodes) > 1) {
            $transporter->setLoadBalancer($serviceBalancer);
        }

        return $transporter;
    }

    public function register($service = null)
    {
        if (is_null($service)) {
            $service = $this->getServices();
        }

        foreach ((array) $service as $serviceName) {
            ServiceManager::register($serviceName, [
                ServiceManager::REGISTRY => $this,
            ]);
        }
    }
}
