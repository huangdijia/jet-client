<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf Jet-client.
 *
 * @link     https://github.com/huangdijia/jet-client
 * @document https://github.com/huangdijia/jet-client/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/jet-client/blob/main/LICENSE
 */
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
     */
    public function pack($data): string
    {
        $data = json_encode($data);

        return $data . $this->eof;
    }

    /**
     * @return mixed
     */
    public function unpack(string $data)
    {
        return json_decode($data, true);
    }
}
