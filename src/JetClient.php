<?php

class JetClient
{
    protected $service;
    /**
     * @var AbstractJetTransporter
     */
    protected $transporter;

    public function __construct($service, $transporter)
    {
        $this->service     = $service;
        $this->transporter = $transporter;
    }

    public function __call($name, $arguments)
    {
        $tries       = 1;
        $path        = JetUtil::generatePath($this->service, $name);
        $data        = JetUtil::formatRequest(array($path, $arguments, uniqid()));
        $transporter = $this->transporter;

        if ($this->transporter->getLoadBalancer()) {
            $tries = count($this->transporter->getLoadBalancer()->getNodes());
        }

        $data = JetUtil::retry($tries, function () use ($transporter, $data) {
            $transporter->send(JetUtil::jsonEofPack($data));
            $ret = $transporter->recv();

            JetUtil::throwIf(!is_string($ret), new RuntimeException('Recv failed'));

            return JetUtil::jsonEofUnpack($ret);
        });

        if (array_key_exists('result', $data)) {
            return $data['result'];
        }

        throw new JetServerException(isset($data['error']) ? $data['error'] : 'Server error');
    }
}
