<?php namespace Valle\Controllers;

use Doctrine\DBAL\Driver;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

use Valle\Models\Users as UsersModel;
use Valle\ResponseMessage\API;
use Valle\Services\ValidatePostProps;

class Users
{
    /** 
     * @var Twig
    */
    protected $view;

    /** 
     * @var UsersModel
    */
    protected $model;

    public function __construct(Twig $view, UsersModel $model)
    {
        $this->view = $view;
        $this->model = $model;
    }

    public function render(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'users/index.twig');
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $response->getBody();

        try {
            $post = (object)$request->getParsedBody();
    
            ValidatePostProps::verify($post, ['user_name', 'user_email', 'user_password', 'user_type_id']);

            $userId = $this->model->create($post);

            $body->write(API::success('users', ['user_id' => $userId]));

            return $response->withStatus(200);
        } catch (\Valle\Models\Exceptions\AlreadyExistsException $e) {
            $body->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        } catch (\Valle\Models\Exceptions\ValidationErrorException $e) {
            $body->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        } catch (\Exception $e) {
            $body->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        }
    }

    public function findOne(Request $request, Response $response, $attrs)
    {
        $body = $response->getBody();

        try {
            $service = $this->model->findOne((int)$attrs['id']);
            
            $body->write(API::success('services', $service->toArray()));

            return $response->withStatus(200);
        } catch (\Valle\Models\Exceptions\NotFoundException $e) {
            $body->write(API::error($e->getMessage()));

            return $response->withStatus(404);

        } catch (\Exception $e) {
            $body->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        }
    }

    public function search(Request $request, Response $response)
    {
        try {
            $params = $request->getQueryParams();

            $users = $this->model->search($params);
    
            $response->getBody()->write(json_encode($users));

            return $response->withStatus(200);
        } catch (\Valle\Models\Exceptions\NotFoundException $e) {
            $response->getBody()->write(API::error($e->getMessage()));

            return $response->withStatus(404);
        } catch (Driver\Exception $e) {
            $response->getBody()->write(API::error('Verifique os parâmetros passados'));

            return $response->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        }
    }
}