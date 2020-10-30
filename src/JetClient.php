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
    protected $dataFormater;
    /**
     * @var JetPathGeneratorInterface
     */
    protected $pathGenerator;

    public function __construct($service, $transporter, $packer = null, $dataFormater = null, $pathGenerator = null)
    {
        if (is_null($packer)) {
            $packer = new JetJsonEofPacker();
        }
        if (is_null($dataFormater)) {
            $dataFormater = new JetDataFormatter();
        }
        if (is_null($pathGenerator)) {
            $pathGenerator = new JetPathGenerator();
        }

        JetUtil::throwIf(!($packer instanceof JetPackerInterface), new InvalidArgumentException('Invaild $packer'));
        JetUtil::throwIf(!($dataFormater instanceof JetDataFormatterInterface), new InvalidArgumentException('Invaild $dataFormater'));
        JetUtil::throwIf(!($pathGenerator instanceof JetPathGeneratorInterface), new InvalidArgumentException('Invaild $pathGenerator'));

        $this->service       = $service;
        $this->transporter   = $transporter;
        $this->packer        = $packer;
        $this->dataFormater  = $dataFormater;
        $this->pathGenerator = $pathGenerator;

    }

    public function __call($name, $arguments)
    {
        $tries       = 1;
        $path        = $this->pathGenerator->generate($this->service, $name);
        $data        = $this->dataFormater->formatRequest(array($path, $arguments, uniqid()));
        $transporter = $this->transporter;
        $packer      = $this->packer;

        if ($this->transporter->getLoadBalancer()) {
            $tries = count($this->transporter->getLoadBalancer()->getNodes());
        }

        $data = JetUtil::retry($tries, function () use ($transporter, $packer, $data) {
            $transporter->send($packer->pack($data));
            $ret = $transporter->recv();

            JetUtil::throwIf(!is_string($ret), new RuntimeException('Recv failed'));

            return $packer->unpack($ret);
        });

        if (array_key_exists('result', $data)) {
            return $data['result'];
        }

        throw new JetServerException(isset($data['error']) ? $data['error'] : 'Server error');
    }
}
