<?php

namespace Huangdijia\Jet\Contract;

interface PackerInterface
{
    public function pack($data): string;
    public function unpack(string $data);
}
