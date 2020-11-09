<?php

namespace Huangdijia\Jet\Contract;

interface PathGeneratorInterface
{
    /**
     * @param string $service 
     * @param string $method 
     * @return string 
     */
    public function generate(string $service, string $method);
}