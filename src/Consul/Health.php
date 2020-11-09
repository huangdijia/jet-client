<?php

namespace Huangdijia\Jet\Consul;

class Health extends Client
{
    /**
     * Get service
     * @param string $service
     * @return Response
     */
    public function service($service = '')
    {
        $params = $this->resolveOptions([], ['dc', 'tag', 'passing']);

        return $this->request('GET', '/v1/health/service/' . $service, $params);
    }
}
