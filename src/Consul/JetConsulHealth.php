<?php

class JetConsulHealth extends JetConsulClient
{
    /**
     * Get service
     * @param string $service
     * @param array $options
     * @return JetConsulResponse
     */
    public function service($service = '', $options = array())
    {
        $options = array(
            'query' => $this->resolveOptions($options, array('dc', 'tag', 'passing')),
        );

        return $this->request('GET', '/v1/health/service/' . $service, $options);
    }
}
