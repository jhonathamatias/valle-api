<?php

namespace Valle\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Valle\Models\SignIn as SignInModel;
use Valle\ResponseMessage\Auth;
use Valle\Services\Auth as ServicesAuth;

class SignIn
{
    /**
     * @var SignInModel
     */
    protected $model;

    public function __construct(SignInModel $model)
    {
        $this->model = $model;
    }

    public function doSignIn(Request $request, Response $response)
    {
        $post = (object)$request->getParsedBody();

        $user = $this->model->doSignIn($post->email, $post->password);

        if (!$user) {
            $response->getBody()->write(Auth::error('Email ou senha inválido'));

            return $response
                ->withStatus(401);
        }
        
        $response->getBody()->write(Auth::success('Sign in successfull', [
            'token' => ServicesAuth::createToken([$user]),
            'user'  => $user
        ]));

        return $response
            ->withStatus(200);
    }

    public function logout(Request $request, Response $response)
    {
        session_destroy();

        return $response->withHeader('Location', '/signin');
    }
}
