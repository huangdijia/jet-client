<?php

class JetConsulAgent extends JetConsulClient
{
    public function registerService($service)
    {
        $options = array(
            'body' => $service,
        );

        return $this->request('PUT', '/v1/agent/service/register?replace-existing-checks=true', $options);
    }
}
