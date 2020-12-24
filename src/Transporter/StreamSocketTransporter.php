<?php

declare(strict_types=1);
/**
 * This file is part of Jet-Client.
 *
 * @link     https://github.com/huangdijia/jet-client
 * @document https://github.com/huangdijia/jet-client/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/jet-client/blob/main/LICENSE
 */
namespace Huangdijia\Jet\Transporter;

use Exception;
use Huangdijia\Jet\Exception\ConnectionException;
use Huangdijia\Jet\Exception\ExceptionThrower;
use Huangdijia\Jet\Exception\RecvFailedException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class StreamSocketTransporter extends AbstractTransporter
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

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function send(string $data)
    {
        $this->connect();
        fwrite($this->client, $data);
    }

    /**
     * @throws Throwable
     * @return string
     */
    public function recv()
    {
        try {
            return $this->receive();
        } catch (Throwable $e) {
            $this->close();
            throw $e;
        }
    }

    /**
     * @return string
     */
    public function receive()
    {
        $buf = '';
        $timeout = 1000;

        stream_set_blocking($this->client, false);

        // The maximum number of retries is 12, and 1000 microseconds is the minimum waiting time.
        // The waiting time is doubled each time until the server writes data to the buffer.
        // Usually, the data can be obtained within 1 microsecond.
        $result = retry(12, function () use (&$buf, &$timeout) {
            $read = [$this->client];
            $write = null;
            $except = null;

            while (stream_select($read, $write, $except, 0, $timeout)) {
                foreach ($read as $r) {
                    $res = fread($r, 8192);
                    if (feof($r)) {
                        return new ExceptionThrower(new ConnectionException('Connection was closed.'));
                    }
                    $buf .= $res;
                }
            }

            if (! $buf) {
                $timeout *= 2;

                throw new RecvFailedException('No data was received');
            }

            return $buf;
        });

        if ($result instanceof ExceptionThrower) {
            throw $result->getThrowable();
        }

        return $result;
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @return (string|int)[]
     */
    protected function getTarget()
    {
        if ($this->getLoadBalancer()) {
            $node = $this->getLoadBalancer()->select();
        } else {
            $node = $this;
        }

        if (! $node->host || ! $node->port) {
            throw new InvalidArgumentException(sprintf('Invalid host %s or port %s.', $node->host, $node->port));
        }

        return [$node->host, $node->port];
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
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

        [$host, $port] = $this->getTarget();

        $client = stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $this->timeout);

        if ($client === false) {
            throw new ConnectionException(sprintf('[%d] %s', $errno, $errstr));
        }

        $this->client = $client;
        $this->isConnected = true;
    }

    protected function close()
    {
        if ($this->client) {
            fclose($this->client);
            $this->client = null;
        }
    }
}
