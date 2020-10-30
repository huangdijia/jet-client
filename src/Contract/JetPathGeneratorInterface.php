<?php

interface JetPathGeneratorInterface
{
    /**
     * @param string $service 
     * @param string $method 
     * @return string 
     */
    public function generate($service, $method);
}