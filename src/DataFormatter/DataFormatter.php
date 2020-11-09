<?php

namespace Huangdijia\Jet\DataFormatter;

use Huangdijia\Jet\Contract\DataFormatterInterface;

class DataFormatter implements DataFormatterInterface
{
    public function formatRequest($data): array
    {
        list($path, $params, $id) = $data;

        return [
            'jsonrpc' => '2.0',
            'method'  => $path,
            'params'  => $params,
            'id'      => $id,
            'data'    => [],
        ];
    }

    public function formatResponse($data): array
    {
        list($id, $result) = $data;

        return [
            'jsonrpc' => '2.0',
            'id'      => $id,
            'result'  => $result,
        ];
    }

    public function formatErrorResponse($data): array
    {
        [$id, $code, $message, $data] = $data;

        if (isset($data) && $data instanceof \Throwable) {
            $data = [
                'class'   => get_class($data),
                'code'    => $data->getCode(),
                'message' => $data->getMessage(),
            ];
        }

        return [
            'jsonrpc' => '2.0',
            'id'      => $id ?? null,
            'error'   => [
                'code'    => $code,
                'message' => $message,
                'data'    => $data,
            ],
        ];
    }
}
