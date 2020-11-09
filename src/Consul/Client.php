<?php

namespace Huangdijia\Jet\Consul;

class Client
{
    protected $baseUri;
    protected $timeout;

    public function __construct(array $options = [])
    {
        $options = array_merge([
            'uri'     => '',
            'timeout' => 2,
        ], $options);

        $this->baseUri = rtrim(array_get($options, 'uri', ''), '/');
        $this->timeout = array_get($options, 'timeout', 1);
    }

    /**
     * @param array $options
     * @param array $availableOptions
     * @return array
     */
    protected function resolveOptions(array $options, array $availableOptions)
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
     * @return Response
     */
    public function request(string $method = 'GET', string $uri = '', array $data = [])
    {
        $url = $this->baseUri . '/' . ltrim($uri, '/');
        $ch  = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

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

        return Response::make($ch);
    }
}
