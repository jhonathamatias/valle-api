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
            $file = (object)$request->getUploadedFiles();
            
            $post->image = $file->image;

            ValidatePostProps::verify($post, [
                'name', 
                'description', 
                'image', 
                'price',
                'product_size_id',
                'product_color_id'
            ]);

            $productId = $this->model->create($post);

            $body->write(API::success('data', [
                'product' => ['id' => $productId],
                'created' => true
            ]));

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

    public function getSizes(Request $request, Response $response): Response
    {
        try {
            $sizes = $this->model->getSizes();
    
            $response->getBody()->write(API::success('sizes', $sizes));

            return $response->withStatus(200);
        } catch (\Valle\Models\Exceptions\NotFoundException $e) {
            $response->getBody()->write(API::error($e->getMessage()));

            return $response->withStatus(404);
        } catch (Driver\Exception $e) {
            $response->getBody()->write(API::error('Houve um erro'));

            return $response->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        }
    }

    public function getColors(Request $request, Response $response): Response
    {
        try {
            $colors = $this->model->getColors();
    
            $response->getBody()->write(API::success('colors', $colors));

            return $response->withStatus(200);
        } catch (\Valle\Models\Exceptions\NotFoundException $e) {
            $response->getBody()->write(API::error($e->getMessage()));

            return $response->withStatus(404);
        } catch (Driver\Exception $e) {
            $response->getBody()->write(API::error('Houve um erro'));

            return $response->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(API::error($e->getMessage()));

            return $response->withStatus(400);
        }
    }
}