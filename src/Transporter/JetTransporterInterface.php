<?php

interface JetTransporterInterface
{
    /**
     * @param string $data 
     * @return void 
     */
    public function send($data);
    /**
     * @return string 
     */
    public function recv();

}