<?php

class JetStreamSocketTransporter extends AbstractJetTransporter
{
    /**
     * @var null|resource
     */
    protected $client;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * @var bool
     */
    protected $isConnected = false;

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param string $data
     * @return void
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws RuntimeException
     */
    public function send($data)
    {
        $this->connect();
        fwrite($this->client, $data);
    }

    /**
     * @return string
     */
    public function recv()
    {
        $buf     = '';
        $timeout = 1000;
        $client  = $this->client;

        stream_set_blocking($this->client, false);

        // The maximum number of retries is 12, and 1000 microseconds is the minimum waiting time.
        // The waiting time is doubled each time until the server writes data to the buffer.
        // Usually, the data can be obtained within 1 microsecond.
        return JetUtil::retry(12, function () use (&$buf, &$timeout, $client) {
            $read   = array($client);
            $write  = null;
            $except = null;
            while (stream_select($read, $write, $except, 0, $timeout)) {
                foreach ($read as $r) {
                    $buf .= fread($r, 8192);
                }
            }

            if (!$buf) {
                $timeout *= 2;

                throw new JetRecvFailedException('No data was received');
            }

            return $buf;
        });
    }

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
     * @return void
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws RuntimeException
     */
    protected function connect()
    {
        if ($this->isConnected) {
            return;
        }
        if ($this->client) {
            fclose($this->client);
            unset($this->client);
        }

        list($host, $port) = $this->getTarget();

        $client = stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $this->timeout);

        JetUtil::throwIf($client === false, new RuntimeException(sprintf('[%d] %s', $errno, $errstr)));

        $this->client      = $client;
        $this->isConnected = true;
    }

    /**
     * @return void
     */
    protected function close()
    {
        if ($this->client) {
            fclose($this->client);
            $this->client = null;
        }
    }
}
