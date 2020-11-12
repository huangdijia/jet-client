<?php

class JetConsulAgent extends JetConsulClient
{
    /**
     * @param array $service
     * @return JetConsulResponse
     */
    public function registerService($service = array())
    {
        $options = array(
            'body' => $service,
        );

        return $this->request('PUT', '/v1/agent/service/register?replace-existing-checks=true', $options);
    }

    /**
     * @param string $serviceId 
     * @return JetConsulResponse 
     */
    public function deregisterService($serviceId)
    {
        return $this->request('PUT', '/v1/agent/service/deregister/' . $serviceId);
    }
}
