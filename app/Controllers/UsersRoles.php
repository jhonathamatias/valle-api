<?php namespace Valle\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Valle\Models\UsersRoles as UsersRolesModel;
use Valle\ResponseMessage\API;

class UsersRoles
{
    public function __construct(protected UsersRolesModel $model)
    {
    }

    public function roles(Request $request, Response $response, array $attrs): Response
    {
        try {
            $userId = $attrs['user_id'];

            $roles = $this->model->roles((int)$userId);

            $response->getBody()->write(API::success('roles', [$roles]));
            return $response->withStatus(200);
        } catch (\Valle\Models\Exceptions\NotFoundException $e) {
            $response->getBody()->write(API::error($e->getMessage()));

            return $response->withStatus(404);

        } catch(\Exception $e) {
            $response->getBody()->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        }
    }
}