<?php

class JetConsulClient
{
    protected $baseUri;
    protected $timeout;

    public function __construct($options = array())
    {
        $options = array_merge(array(
            'uri'     => '',
            'timeout' => 2,
        ), $options);

        $this->baseUri = rtrim(JetUtil::arrayGet($options, 'uri', ''), '/');
        $this->timeout = JetUtil::arrayGet($options, 'timeout', 1);
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

        // $response = curl_exec($ch);

        // JetUtil::throwIf(curl_errno($ch), new RuntimeException(curl_error($ch)));

        // curl_close($ch);

        // return JetUtil::tap(json_decode($response, true), function ($decoded) {
        //     JetUtil::throwIf(!is_array($decoded), new RuntimeException('Parse response failed'));
        // });

        return JetConsulResponse::make($ch);
    }
}
