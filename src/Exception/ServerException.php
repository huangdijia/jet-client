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
namespace Huangdijia\Jet\Exception;

use Throwable;

class ServerException extends JetException
{
    /**
     * @var array
     */
    protected $error;

    public function __construct(array $error = [], Throwable $previous = null)
    {
        $code = $error['data']['error'] ?? $error['code'] ?? 0;
        $message = $error['data']['message'] ?? $error['message'] ?? 'Server Error';

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
