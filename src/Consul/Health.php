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

class Health extends Client
{
    /**
     * Get service.
     * @param string $service
     * @return Response
     */
    public function service($service = '', array $options = [])
    {
        $params = [
            'query' => $this->resolveOptions($options, ['dc', 'tag', 'passing']),
        ];

        return $this->request('GET', '/v1/health/service/' . $service, $params);
    }
}
