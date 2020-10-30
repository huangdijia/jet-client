<?php

class JetConsulHealth extends JetConsulClient
{
    /**
     * Get service
     * @param string $service 
     * @return array 
     */
    public function service($service = '')
    {
        $params = array();

        return $this->request('GET', '/v1/health/service/' . $service, $params);
    }
}