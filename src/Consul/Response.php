<?php

namespace Huangdijia\Jet\Consul;

use Huangdijia\Jet\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @param string|null $key 
     * @param mixed|null $default 
     * @return mixed 
     * @throws ServerException 
     */
    public function json(string $key = null, $default = null)
    {
        if ($this->response->getHeaderLine('Content-Type') !== 'application/json') {
            throw new ServerException(['message' => 'The Content-Type of response is not equal application/json']);
        }

        $data = json_decode((string) $this->response->getBody(), true);

        if (!$key) {
            return $data;
        }

        return array_get($data, $key, $default);
    }

    /**
     * @return ResponseInterface 
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function __call($name, $arguments)
    {
        return $this->response->{$name}(...$arguments);
    }
}
