<?php

class JetServerException extends JetException
{
    /**
     * @var array
     */
    protected $error;

    /**
     * @param array $error
     * @param Exception|null $previous
     * @return void
     */
    public function __construct($error = array(), $previous = null)
    {
        $code    = isset($error['code']) ? $error['code'] : 0;
        $message = isset($error['message']) ? $error['message'] : 'Server Error';

        $this->error = $error;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }
}
