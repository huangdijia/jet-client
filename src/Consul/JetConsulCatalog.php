<?php

class JetConsulCatalog extends JetConsulClient
{
    /**
     * @return JetConsulResponse 
     * @throws InvalidArgumentException 
     * @throws Exception 
     */
    public function services()
    {
        $params = $this->resolveOptions(array(), array('dc'));

        return $this->request('GET', '/v1/catalog/services', $params);
    }
}