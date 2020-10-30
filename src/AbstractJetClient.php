<?php

abstract class AbstractJetClient
{
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->client, $name), $arguments);
    }
}
