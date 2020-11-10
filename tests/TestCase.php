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
namespace Huangdijia\Jet\Tests;

use Huangdijia\Jet\Registry\ConsulRegistry;
use Huangdijia\Jet\Transporter\GuzzleHttpTransporter;
use Huangdijia\Jet\Transporter\StreamSocketTransporter;

/**
 * @internal
 * @coversNothing
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createGuzzleHttpTransporter()
    {
        return new GuzzleHttpTransporter('127.0.0.1', 9502, ['timeout' => 2]);
    }

    public function createStreamSocketTransporter()
    {
        return new StreamSocketTransporter('127.0.0.1', 9503, 2);
    }

    protected function createRegistry()
    {
        return new ConsulRegistry('127.0.0.1', 8500, 2);
    }
}
