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
namespace Huangdijia\Jet\Consul;

class Agent extends Client
{
    public function registerService(array $service): Response
    {
        $params = [
            'body' => json_encode($service),
        ];

        return $this->request('PUT', '/v1/agent/service/register', $params);
    }
}
