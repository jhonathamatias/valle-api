<?php namespace Valle\Controllers;

use Doctrine\DBAL\Driver;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

use Valle\Models\Products as ProductsModel;
use Valle\ResponseMessage\API;
use Valle\Services\ValidatePostProps;

class Products
{
    public function __construct(protected ProductsModel $model)
    {
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $response->getBody();

        try {
            $post = (object)$request->getParsedBody();
    
            ValidatePostProps::verify($post, ['name', 'description', 'image', 'price']);

            $productId = $this->model->create($post);

            $body->write(API::success('products', ['id' => $productId]));

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
            
            $body->write(API::success('products', $service->toArray()));

            return $response->withStatus(200);
        } catch (\Valle\Models\Exceptions\NotFoundException $e) {
            $body->write(API::error($e->getMessage()));

            return $response->withStatus(404);

        } catch (\Exception $e) {
            $body->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        }
    }

    public function search(Request $request, Response $response): Response
    {
        try {
            $params = $request->getQueryParams();

            $products = $this->model->search($params);
    
            $response->getBody()->write(API::search($products, 'products'));

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