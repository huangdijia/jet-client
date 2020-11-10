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

use GuzzleHttp\Exception\GuzzleException;
use Huangdijia\Jet\Exception\ClientException;
use Huangdijia\Jet\Exception\ServerException;

class Catalog extends Client
{
    /**
     * @throws ServerException
     * @throws ClientException
     * @throws GuzzleException
     * @return Response
     */
    public function services(array $options = [])
    {
        $params = [
            'query' => $this->resolveOptions($options, ['dc']),
        ];

        return $this->request('GET', '/v1/catalog/services', $params);
    }
}
