<?php

namespace Valle\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Valle\Services\Auth;
use Valle\ResponseMessage\Auth as ResponseMessageAuth;

class User
{
    public function signIn(Request $request, Response $response)
    {
        $post = (object)$request->getParsedBody();

        $user = [
            'id' => 1,
            'name' => 'Jhonatha Matias',
            'email' => 'jhonathamatias35@gmail.com'
        ];

        if (
            $post->email !== $user['email'] ||
            $post->password !== '123456'
        ) {
            $response->getBody()->write(ResponseMessageAuth::error('Email or Password Invalid'));
            return $response
                ->withStatus(403);
        }

        $response->getBody()->write(
            ResponseMessageAuth::success(
                'User Authenticated',
                [
                    'token' => Auth::createToken($user),
                    'user'  => $user
                ]
            )
        );

        return $response
            ->withStatus(200);
    }

    public function get(Request $request, Response $response, $attrs)
    {
        $payload = json_encode([
            'id' => 1,
            'name' => 'Jhonatha Matias',
            'email' => 'jhonathamatias35@gmail.com'
        ]);

        $response->getBody()->write($payload);

        return $response->withStatus(400);
    }

    public function getAll(Request $request, Response $response)
    {
        $payload = json_encode([
            [
                'id' => 1,
                'name' => 'Jhonatha Matias',
                'email' => 'jhonathamatias35@gmail.com'
            ],
            [
                'id' => 2,
                'name' => 'Patrick Matias',
                'email' => 'patrick@gmail.com'
            ],
            [
                'id' => 3,
                'name' => 'Rosa Maria',
                'email' => 'rosamaria@gmail.com'
            ]
        ]);

        $response->getBody()->write($payload);

        return $response->withStatus(200);
    }
}
