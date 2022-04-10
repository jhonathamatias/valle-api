<?php
namespace Valle\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Doctrine\DBAL\Driver;

use Valle\Models\Collaborators as CollaboratorsModel;
use Valle\ResponseMessage\API;
use Valle\Services\ValidatePostProps;

class Collaborators
{
    const ENTITY = 'collaborators';

    public function __construct(protected Twig $view, protected CollaboratorsModel $model) {}

    public function render(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'collaborators/index.twig');
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $response->getBody();
        
        try {
            $post = (object)$request->getParsedBody();

            ValidatePostProps::verify($post, ['collaborator']);
            ValidatePostProps::verify((object)$post->collaborator, ['user_id', 'occupation_id']);

            $id = $this->model->create($post);

            $body->write(API::success(self::ENTITY, ['id' => $id]));
            sleep(3);
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

    public function search(Request $request, Response $response)
    {
        try {
            $params = $request->getQueryParams();

            $data = $this->model->search($params);
    
            $response->getBody()->write(API::success(self::ENTITY, $data));

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

    public function getOccupation(Request $request, Response $response)
    {
        try {
            $data = $this->model->getOccupation();
    
            $response->getBody()->write(API::success('occupation', $data));

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