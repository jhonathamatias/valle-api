<?php

namespace Valle\Models;

use DateTimeImmutable;
use Valle\Factorys\RepositoryFactory;
use Valle\Models\Exceptions\AlreadyExistsException;
use Valle\Models\Exceptions\ErrorInsertException;
use Valle\Models\Exceptions\NotFoundException;
use Valle\Models\Exceptions\ValidationErrorException;
use Respect\Validation\Validator as v;
use Valle\Interfaces\MapperSearchInterface;
use Valle\Services\Mapper;

class Products
{
    public function __construct(protected RepositoryFactory $repository)
    {
    }

    public function create(object $post): int
    {
        $productsRepository = $this->repository->get('products');

        $post->created_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        $post->updated_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        $this->validationProduct($post);

        $productsRepository->insert([
            'name'          => $post->name,
            'description'   => $post->description,
            'image'         => $post->image,
            'price'         => $post->price,
            'created_at'    => $post->created_at,
            'updated_at'    => $post->updated_at
        ]);

        $id = $productsRepository->getLastInsertId();

        if ((bool)$id === false) {
            throw new ErrorInsertException('Erro ao criar o produto');
        }

        return $id;
    }

    public function alreadyExists(string $name): bool
    {
        $productsRepository = $this->repository->get('products');

        $product = $productsRepository->where('name = ?', [$name])[0] ?? null;

        return $product !== null;
    }

    /**
     * @return array
     */
    public function search(array $params = []): MapperSearchInterface
    {
        $productsRepository = $this->repository->get('products');

        $products = $productsRepository->search($params);

        if ($products->total() === 0) {
            throw new NotFoundException('Não foi encontrado nenhum produto');
        }

        return $products;
    }

    public function validationProduct(object $product): bool
    {
        try {
            $serviceValidator = v::attribute('name', v::stringType()->notEmpty())
                ->attribute('description', v::stringType()->notEmpty())
                ->attribute('image', v::stringType()->notEmpty())
                ->attribute('price', v::floatVal())
                ->attribute('created_at', v::dateTime('Y-m-d H:i:s'))
                ->attribute('updated_at', v::dateTime('Y-m-d H:i:s'));

            $serviceValidator->assert($product);

            return true;
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ValidationErrorException(json_encode($e->getMessages()));
        }
    }
}
