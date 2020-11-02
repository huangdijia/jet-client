<?php

class JetConsulHealth extends JetConsulClient
{
    /**
     * Get service
     * @param string $service
     * @return JetConsulResponse
     */
    public function service($service = '')
    {
        $params = $this->resolveOptions(array(), array('dc', 'tag', 'passing'));

        return $this->request('GET', '/v1/health/service/' . $service, $params);
    }
}
