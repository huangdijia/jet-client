<?php

class JetJsonEofPacker implements JetPackerInterface
{
    /**
     * @var string
     */
    protected $eof;

    public function __construct($eof = "\r\n")
    {
        $this->eof = $eof;
    }

    /**
     * @param mixed $data 
     * @return string 
     */
    public function pack($data)
    {
        $data = json_encode($data);

        return $data . $this->eof;
    }

    /**
     * @param string $data 
     * @return array 
     */
    public function unpack($data)
    {
        return json_decode($data, true);
    }
}