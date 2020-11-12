<?php

use Huangdijia\Jet\ServiceManager;

class JetClient
{
    protected $service;
    /**
     * @var AbstractJetTransporter
     */
    protected $transporter;
    /**
     * @var JetPackerInterface
     */
    protected $packer;
    /**
     * @var JetDataFormatterInterface
     */
    protected $dataFormatter;
    /**
     * @var JetPathGeneratorInterface
     */
    protected $pathGenerator;
    /**
     * @var int
     */
    protected $tries;

    /**
     * @param mixed $service
     * @param AbstractJetTransporter $transporter
     * @param JetPackerInterface|null $packer
     * @param JetDataFormatterInterface|null $dataFormatter
     * @param JetPathGeneratorInterface|null $pathGenerator
     * @param int|null $tries
     * @return void
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct($service, $transporter, $packer = null, $dataFormatter = null, $pathGenerator = null, $tries = null)
    {
        if (is_null($packer)) {
            $packer = new JetJsonEofPacker();
        }
        if (is_null($dataFormatter)) {
            $dataFormatter = new JetDataFormatter();
        }
        if (is_null($pathGenerator)) {
            $pathGenerator = new JetPathGenerator();
        }
        if (is_null($tries)) {
            $tries = 1;
        }

        JetServiceManager::assertTransporter($transporter);
        JetServiceManager::assertPacker($packer);
        JetServiceManager::assertDataFormatter($dataFormatter);
        JetServiceManager::assertPathGenerator($pathGenerator);
        JetServiceManager::assertTries($tries);

        $this->service       = $service;
        $this->transporter   = $transporter;
        $this->packer        = $packer;
        $this->dataFormatter = $dataFormatter;
        $this->pathGenerator = $pathGenerator;
        $this->tries         = $tries;

    }

    public function __call($name, $arguments)
    {
        $tries         = $this->tries;
        $path          = $this->pathGenerator->generate($this->service, $name);
        $transporter   = $this->transporter;
        $dataFormatter = $this->dataFormatter;
        $packer        = $this->packer;

        if ($this->transporter->getLoadBalancer()) {
            $nodeCount = count($this->transporter->getLoadBalancer()->getNodes());
            if ($nodeCount > $tries) {
                $tries = $nodeCount;
            }
        }

        return JetUtil::retry($tries, function () use ($transporter, $dataFormatter, $packer, $path, $arguments) {
            $data = $dataFormatter->formatRequest(array($path, $arguments, uniqid()));

            $transporter->send($packer->pack($data));

            $ret = $transporter->recv();

            JetUtil::throwIf(!is_string($ret), new JetRecvFailedException('Recv failed'));

            return JetUtil::with($packer->unpack($ret), function ($data) {
                if (array_key_exists('result', $data)) {
                    return $data['result'];
                }

                throw new JetServerException(isset($data['error']) ? $data['error'] : 'Server error');
            });
        });

    }
}
