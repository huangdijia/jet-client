<?php

namespace Huangdijia\Jet\Consul;

use Huangdijia\Jet\Exception\ServerException;
use Huangdijia\Jet\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class Catalog extends Client
{
    /**
     * 
     * @param array $options 
     * @return Response 
     * @throws ServerException 
     * @throws ClientException 
     * @throws GuzzleException 
     */
    public function services(array $options = [])
    {
        $params = [
            'query' => $this->resolveOptions($options, ['dc']),
        ];

        return $this->request('GET', '/v1/catalog/services', $params);
    }
}
