<?php

abstract class AbstractJetClient
{
    /**
     * @var JetClient
     */
    protected $client;

    /**
     * @param JetClient $client
     * @return void
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct($client)
    {
        JetUtil::throwIf(!($client instanceof JetClient), new InvalidArgumentException('argument $client must instanceof JetClient'));

        $this->client = $client;
    }

    /**
     * @param string $name 
     * @param array $arguments 
     * @return mixed 
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->client, $name), $arguments);
    }
}
