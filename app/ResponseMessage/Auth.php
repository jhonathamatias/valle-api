<?php

namespace Valle\ResponseMessage;

class Auth
{
    public static function error(string $message)
    {
        return json_encode([
            'authenticated' => false,
            'error' => [
                'message' => $message,
            ]
        ]);
    }

    public static function success(string $message, array $data = [])
    {
        return json_encode([
            'authenticated' => true,
            'details' => $data
        ]);
    }
}