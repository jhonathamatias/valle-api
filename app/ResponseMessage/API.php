<?php

namespace Valle\ResponseMessage;

use JsonSerializable;

class API
{
    public static function error(string $message)
    {
        return json_encode([
            'error' => [
                'message' => $message,
            ]
        ]);
    }

    public static function success(string $attr = 'data', array $data = [], string $message = '')
    {
        return json_encode([
            $attr => $data
        ]);
    }

    public static function search(\Valle\Interfaces\MapperSearchInterface $mapper, string $entityName = 'data'): string
    {
        return json_encode([
            $entityName => $mapper->data(),
            'total'     => $mapper->total(),
            'offset'    => $mapper->offset(),
            'limit'     => $mapper->limit()
        ]);
    }
}
