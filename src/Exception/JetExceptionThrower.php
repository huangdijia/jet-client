<?php

final class JetExceptionThrower
{
    /**
     * @var Exception
     */
    private $e;

    public function __construct(Exception $e)
    {
        $this->e = $e;
    }

    /**
     * @return Exception 
     */
    public function getThrowable()
    {
        return $this->e;
    }
}