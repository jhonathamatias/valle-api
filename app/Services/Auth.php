<?php namespace Valle\Services;

use Firebase\JWT\JWT;

class Auth
{
    public static function createToken(array $user): string
    {
        return JWT::encode($user, getenv('APPLICATION_SECRET_KEY'));
    }

    public static function verifyToken(string $token)
    {
        $splitToken = explode(" ", $token);

        try {
            return JWT::decode($splitToken[1], getenv('APPLICATION_SECRET_KEY'), ['HS256']);
        } catch(\Exception $e) {
            return false;
        }
    }
}