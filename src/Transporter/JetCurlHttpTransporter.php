<?php

class JetCurlHttpTransporter extends AbstractJetTransporter
{
    /**
     * @var string
     */
    protected $response;

    /**
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function getTarget()
    {
        if ($this->getLoadBalancer()) {
            $node = $this->getLoadBalancer()->select();
        } else {
            $node = $this;
        }

        JetUtil::throwIf(
            !$node->host || !$node->port,
            new InvalidArgumentException(sprintf('Invalid host %s or port %s.', $node->host, $node->port))
        );

        return array($node->host, $node->port);
    }

    /**
     * @param string $data 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws Exception 
     */
    public function send($data)
    {
        list($host, $port) = $this->getTarget();

        $url     = sprintf('http://%s:%d', $host, $port);
        $headers = array(
            'Content-Type: application/json',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        if (preg_match('/^https:\/\//', $url)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        JetUtil::throwIf(curl_errno($ch), new RuntimeException(curl_error($ch)));

        $this->response = $response;
    }

    /**
     * @return string 
     */
    public function recv()
    {
        return $this->response;
    }
}
