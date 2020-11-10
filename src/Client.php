<?php

declare(strict_types=1);
/**
 * This file is part of Jet-Client.
 *
 * @link     https://github.com/huangdijia/jet-client
 * @document https://github.com/huangdijia/jet-client/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/jet-client/blob/main/LICENSE
 */
namespace Huangdijia\Jet;

use Huangdijia\Jet\Contract\DataFormatterInterface;
use Huangdijia\Jet\Contract\PackerInterface;
use Huangdijia\Jet\Contract\PathGeneratorInterface;
use Huangdijia\Jet\Contract\TransporterInterface;
use Huangdijia\Jet\DataFormatter\DataFormatter;
use Huangdijia\Jet\Exception\RecvFailedException;
use Huangdijia\Jet\Exception\ServerException;
use Huangdijia\Jet\Packer\JsonEofPacker;
use Huangdijia\Jet\PathGenerator\PathGenerator;

class Client
{
    protected $service;

    /**
     * @var TransporterInterface
     */
    protected $transporter;

    /**
     * @var PackerInterface
     */
    protected $packer;

    /**
     * @var DataFormatterInterface
     */
    protected $dataFormatter;

    /**
     * @var PathGeneratorInterface
     */
    protected $pathGenerator;

    /**
     * @var int
     */
    protected $tries;

    public function __construct(string $service, TransporterInterface $transporter, PackerInterface $packer = null, DataFormatterInterface $dataFormatter = null, PathGeneratorInterface $pathGenerator = null, int $tries = 1)
    {
        $this->service = $service;
        $this->transporter = $transporter;
        $this->packer = $packer ?? new JsonEofPacker();
        $this->dataFormatter = $dataFormatter ?? new DataFormatter();
        $this->pathGenerator = $pathGenerator ?? new PathGenerator();
        $this->tries = $tries;
    }

    public function __call($name, $arguments)
    {
        $tries = $this->tries;
        $path = $this->pathGenerator->generate($this->service, $name);
        $transporter = $this->transporter;
        $dataFormatter = $this->dataFormatter;
        $packer = $this->packer;

        if ($this->transporter->getLoadBalancer()) {
            $nodeCount = count($this->transporter->getLoadBalancer()->getNodes());
            if ($nodeCount > $tries) {
                $tries = $nodeCount;
            }
        }

        return retry($tries, function () use ($transporter, $dataFormatter, $packer, $path, $arguments) {
            $data = $dataFormatter->formatRequest([$path, $arguments, uniqid()]);

            $transporter->send($packer->pack($data));

            $ret = $transporter->recv();

            throw_if(! is_string($ret), new RecvFailedException('Recv failed'));

            return with($packer->unpack($ret), function ($data) {
                if (array_key_exists('result', $data)) {
                    return $data['result'];
                }

                throw new ServerException($data['error'] ?? 'Server error');
            });
        });
    }
}
