<?php

namespace Huangdijia\Jet\Exception;

use Throwable;

class ServerException extends Exception
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
    public function __construct(array $error = [], Throwable $previous = null)
    {
        $code    = $error['code'] ?? 0;
        $message = $error['message'] ?? 'Server Error';

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
