<?php

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

    public function __construct($service, $transporter, $packer = null, $dataFormatter = null, $pathGenerator = null)
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

        JetUtil::throwIf(!($packer instanceof JetPackerInterface), new InvalidArgumentException('Invaild $packer'));
        JetUtil::throwIf(!($dataFormatter instanceof JetDataFormatterInterface), new InvalidArgumentException('Invaild $dataFormatter'));
        JetUtil::throwIf(!($pathGenerator instanceof JetPathGeneratorInterface), new InvalidArgumentException('Invaild $pathGenerator'));

        $this->service       = $service;
        $this->transporter   = $transporter;
        $this->packer        = $packer;
        $this->dataFormatter = $dataFormatter;
        $this->pathGenerator = $pathGenerator;

    }

    public function __call($name, $arguments)
    {
        $tries         = 1;
        $path          = $this->pathGenerator->generate($this->service, $name);
        $transporter   = $this->transporter;
        $dataFormatter = $this->dataFormatter;
        $packer        = $this->packer;

        if ($this->transporter->getLoadBalancer()) {
            $tries = count($this->transporter->getLoadBalancer()->getNodes());
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
