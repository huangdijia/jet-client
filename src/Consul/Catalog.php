<?php

namespace Huangdijia\Jet\Consul;

class Catalog extends Client
{
    /**
     * @return Response 
     * @throws InvalidArgumentException 
     * @throws Exception 
     */
    public function services()
    {
        $params = $this->resolveOptions([], ['dc']);

        return $this->request('GET', '/v1/catalog/services', $params);
    }
}