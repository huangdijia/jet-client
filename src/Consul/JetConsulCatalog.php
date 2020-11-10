<?php

class JetConsulCatalog extends JetConsulClient
{
    /**
     * @param array $options
     * @return JetConsulResponse
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function services($options = array())
    {
        $options = array(
            'query' => $this->resolveOptions($options, array('dc')),
        );

        return $this->request('GET', '/v1/catalog/services', $options);
    }
}
