<?php

namespace Huangdijia\Jet\Packer;

use Huangdijia\Jet\Contract\PackerInterface;

class JsonEofPacker implements PackerInterface
{
    /**
     * @var string
     */
    protected $eof;

    public function __construct(string $eof = "\r\n")
    {
        $this->eof = $eof;
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function pack($data): string
    {
        $data = json_encode($data);

        return $data . $this->eof;
    }

    /**
     * @param string $data
     * @return array
     */
    public function unpack(string $data)
    {
        return json_decode($data, true);
    }
}
