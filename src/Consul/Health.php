<?php

namespace Huangdijia\Jet\Consul;

class Health extends Client
{
    /**
     * Get service
     * @param string $service
     * @param array $options
     * @return Response
     */
    public function service($service = '', array $options = [])
    {
        $params = [
            'query' => $this->resolveOptions($options, ['dc', 'tag', 'passing']),
        ];

        return $this->request('GET', '/v1/health/service/' . $service, $params);
    }
}
