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
use Valle\Mappers\Search;
use Valle\Services\File;

class Products
{
    public function __construct(
        protected RepositoryFactory $repository,
        protected File $file,
    ) {}
    
    public function create(object $post): int
    {
        $productsRepository = $this->repository->get('products');

        $dateNow = new DateTimeImmutable('now');
        $post->created_at = $dateNow->format('Y-m-d H:i:s');
        $post->updated_at = $dateNow->format('Y-m-d H:i:s');

        $pathImage = $this->file->upload($post->image, 'products'); 
        
        $productsRepository->insert([
            'name'              => $post->name,
            'description'       => $post->description,
            'image'             => $pathImage,
            'price'             => $post->price,
            'product_size_id'   => $post->product_size_id,
            'product_color_id'  => $post->product_color_id,
            'created_at'        => $post->created_at,
            'updated_at'        => $post->updated_at
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

        $productsData = $products->data();

        foreach ($productsData as $key => $product) {
            $productsData[$key]['image'] = $this->file->getImageUrl($product['image']);
        }

        return new Search($productsData, $products->limit(), $products->offset(), $products->total());
    }

    public function productValidation(object $product): bool
    {
        try {
            $serviceValidator = v::attribute('name', v::stringType()->notEmpty())
                ->attribute('description', v::stringType()->notEmpty())
                ->attribute('image', v::stringType()->notEmpty())
                ->attribute('price', v::floatVal())
                ->attribute('product_size_id', v::intType()->notEmpty())
                ->attribute('product_color_id', v::intType()->notEmpty())
                ->attribute('created_at', v::dateTime('Y-m-d H:i:s'))
                ->attribute('updated_at', v::dateTime('Y-m-d H:i:s'));

            $serviceValidator->assert($product);

            return true;
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ValidationErrorException(json_encode($e->getMessages()));
        }
    }

    public function hasColor(string $name): bool
    {
        $productColorRepository = $this->repository->get('product_color');

        $color = $productColorRepository->where('name = ?', [$name])[0] ?? null;

        return $color !== null;
    }

    public function createColor(string $name, string $color)
    {
        $productColorRepository = $this->repository->get('product_color');

        $productColorRepository->insert([
            'name'  => $name,
            'color' => $color
        ]);

        $id = $productColorRepository->getLastInsertId();

        if ((bool)$id === false) {
            throw new ErrorInsertException('Erro ao adicionar cor');
        }

        return $id;
    }

    /**
     * @return array
     */
    public function getSizes(): array
    {
        $productSizeRepository = $this->repository->get('product_size');

        $sizes = $productSizeRepository->getAll();

        if (count($sizes) === 0) {
            throw new NotFoundException('Não existe tamanhos');
        }

        return $sizes;
    }

    /**
     * @return array
     */
    public function getColors(): array
    {
        $productColorRepository = $this->repository->get('product_color');

        $colors = $productColorRepository->getAll();

        if (count($colors) === 0) {
            throw new NotFoundException('Não existe cores');
        }

        return $colors;
    }
}
