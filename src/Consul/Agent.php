<?php

namespace Huangdijia\Jet\Consul;

class Agent extends Client
{
    public function registerService(array $service): Response
    {
        $params = [
            'body' => json_encode($service),
        ];

        return $this->request('PUT', '/v1/agent/service/register', $params);
    }
}
