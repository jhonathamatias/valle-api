<?php

namespace Valle\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Valle\ResponseMessage\Auth as ResponseMessageAuth;
use Valle\Services\Auth as ServicesAuth;

class SignInVerifyMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();

        $response = new Response();

        if (false === isset($_SESSION['auth'])) {
            return $response->withHeader('Location', '/signin');
        }

        if ($_SESSION['auth'] != 'On') {
            return $response->withHeader('Location', '/signin');
        }

        $response->getBody()->write($existingContent);

        return $response;
    }
}
