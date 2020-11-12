<?php

class JetConsulAgent extends JetConsulClient
{
    public function registerService($service, $options = array())
    {
        $options = array(
            'body' => $this->resolveOptions($options, array('dc', 'tag', 'passing')),
        );

        return $this->request('PUT', '/v1/agent/service/register', $options);
    }
}