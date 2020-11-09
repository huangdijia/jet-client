<?php

namespace Huangdijia\Jet\Contract;

interface PackerInterface
{
    public function pack($data);
    public function unpack(string $data);
}