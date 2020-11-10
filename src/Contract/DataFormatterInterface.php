<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf Jet-client.
 *
 * @link     https://github.com/huangdijia/jet-client
 * @document https://github.com/huangdijia/jet-client/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/jet-client/blob/main/LICENSE
 */
namespace Huangdijia\Jet\Contract;

interface DataFormatterInterface
{
    /**
     * @param array $data [$path, $params, $id]
     */
    public function formatRequest($data): array;

    /**
     * @param array $data [$id, $result]
     */
    public function formatResponse($data): array;

    /**
     * @param array $data [$id, $code, $message, $exception]
     */
    public function formatErrorResponse($data): array;
}
