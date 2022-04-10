<?php
namespace Valle\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Doctrine\DBAL\Driver;

use Valle\Models\Company as CompanyModel;
use Valle\ResponseMessage\API;
use Valle\Services\ValidatePostProps;

class Company
{
    const ENTITY = 'company';

    public function __construct(protected Twig $view, protected CompanyModel $model) {}

    public function render(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'company/index.twig');
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $response->getBody();

        try {
            $post = (object)$request->getParsedBody();

            ValidatePostProps::verify($post, ['company']);
            ValidatePostProps::verify((object)$post->company, ['name', 'email', 'phone', 'document']);

            $companyId = $this->model->create($post);

            $body->write(API::success(self::ENTITY, ['id' => $companyId]));
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
            var_dump($e->__toString()); exit;
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
}