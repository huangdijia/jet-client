<?php

class JetConsulClient
{
    protected $baseUri;
    protected $timeout;
    protected $headers;

    public function __construct($options = array())
    {
        $options = array_merge(array(
            'uri'     => '',
            'timeout' => 2,
            'headers' => array(), // array('X-Consul-Token' => 'your-token')
        ), $options);

        $this->baseUri = rtrim(JetUtil::arrayGet($options, 'uri', ''), '/');
        $this->timeout = JetUtil::arrayGet($options, 'timeout', 1);
        $this->headers = JetUtil::arrayGet($options, 'headers', array());
    }

    /**
     * @param array $options
     * @param array $availableOptions
     * @return array
     */
    protected function resolveOptions($options, $availableOptions)
    {
        // Add key of ACL token to $availableOptions
        $availableOptions[] = 'token';

        return array_intersect_key($options, array_flip($availableOptions));
    }

    /**
     * Request
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return JetConsulResponse
     */
    public function request($method = 'GET', $uri = '', $data = array())
    {
        $url = $this->baseUri . '/' . ltrim($uri, '/');
        $ch  = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        if ($this->headers) {
            $headers = array_map(function($k, $v) {
                return "{$k}: {$v}";
            }, array_keys($this->headers), array_values($this->headers));;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (preg_match('/^https:\/\//', $url)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'GET':
            default:
                break;
        }

        return new JetConsulResponse($ch);
    }
}
