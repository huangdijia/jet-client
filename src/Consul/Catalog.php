<?php

namespace Huangdijia\Jet\Consul;

class Catalog extends Client
{
    /**
     * @param array $options
     * @return Response
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function services(array $options = [])
    {
        $params = [
            'query' => $this->resolveOptions($options, ['dc']),
        ];

        return $this->request('GET', '/v1/catalog/services', $params);
    }
}
