<?php

class JetConsulCatalog extends JetConsulClient
{
    /**
     * @return array 
     * @throws InvalidArgumentException 
     * @throws Exception 
     */
    public function services()
    {
        $params = array();

        return $this->request('GET', '/v1/catalog/services', $params);
    }
}