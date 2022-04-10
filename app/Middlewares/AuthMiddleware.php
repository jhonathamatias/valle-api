<?php

namespace Valle\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Valle\ResponseMessage\Auth as ResponseMessageAuth;
use Valle\Services\Auth as ServicesAuth;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();
        $statusCode = $response->getStatusCode();

        $result = $this->validateAuthorization($request->getHeaders());

        $response = new Response();
        
        if (false === json_decode($result)->authenticated) {
            $response
                ->getBody()
                ->write($result);

            return $response
                ->withStatus(401);
        }

        $response->getBody()->write($existingContent);

        return $response->withStatus($statusCode);
    }

    protected function validateAuthorization(array $headers)
    {
        if (false === isset($headers['Authorization'])) {
            return ResponseMessageAuth::error('No Token Provided');
        }

        $token = $headers['Authorization'][0];

        if (false === $this->isFormattedToken($token)) {
            return ResponseMessageAuth::error('Token Malformatted');
        }

        $validToken = ServicesAuth::verifyToken($token);

        if (false === $validToken) {
            return ResponseMessageAuth::error('Token Invalid');
        }

        return ResponseMessageAuth::success('Authentication Accepted', (array)$validToken);
    }

    protected function isFormattedToken(string $token): bool
    {
        $splitToken = explode(" ", $token);

        if (!count($splitToken) === 2) {
            return false;
        }

        return (bool)preg_match('/^Bearer/', $token);
    }
}
