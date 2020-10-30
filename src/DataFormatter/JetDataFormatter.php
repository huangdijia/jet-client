<?php

class JetDataFormatter implements JetDataFormatterInterface
{
    public function formatRequest($data)
    {
        list($path, $params, $id) = $data;

        return array(
            'jsonrpc' => '2.0',
            'method'  => $path,
            'params'  => $params,
            'id'      => $id,
            'data'    => array(),
        );
    }

    public function formatResponse($data)
    {
        list($id, $result) = $data;

        return array(
            'jsonrpc' => '2.0',
            'id'      => $id,
            'result'  => $result,
        );
    }

    public function formatErrorResponse($data)
    {
        list($id, $code, $message, $data) = $data;

        if (isset($data) && $data instanceof Exception) {
            $data = array(
                'class'   => get_class($data),
                'code'    => $data->getCode(),
                'message' => $data->getMessage(),
            );
        }

        return array(
            'jsonrpc' => '2.0',
            'id'      => isset($id) ? $id : null,
            'error'   => array(
                'code'    => $code,
                'message' => $message,
                'data'    => $data,
            ),
        );
    }
}
