<?php

namespace Valle\Models;

use DateTimeImmutable;
use Valle\Factorys\RepositoryFactory;
use Valle\Models\Exceptions\AlreadyExistsException;
use Valle\Models\Exceptions\ErrorInsertException;
use Valle\Models\Exceptions\NotFoundException;
use Valle\Models\Exceptions\ValidationErrorException;
use Valle\Resource\Service as ServiceResource;
use Respect\Validation\Validator as v;

class Users
{
    public function __construct(protected RepositoryFactory $repository)
    {
    }

    public function create(object $post): int
    {
        $usersRepository = $this->repository->get('users');

        $post->created_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        if ($this->alreadyExists($post->user_email) === true) {
            throw new AlreadyExistsException('Já existe um usúario com esse email.');
        }

        $this->validationUser($post);

        $usersRepository->insert([
            'name'          => $post->user_name,
            'email'         => $post->user_email,
            'password'      => password_hash($post->user_password, PASSWORD_BCRYPT),
            'user_type_id'  => $post->user_type_id,
            'created_at'    => $post->created_at
        ]);

        $id = $usersRepository->getLastInsertId();

        if ((bool)$id === false) {
            throw new ErrorInsertException('Erro ao inserir o serviço');
        }

        return $id;
    }

    public function alreadyExists(string $email): bool
    {
        $usersRepository = $this->repository->get('users');

        $user = $usersRepository->where('email = ?', [$email])[0] ?? null;

        return $user !== null;
    }

    public function findOne(int $id): ServiceResource
    {
        $usersRepository = $this->repository->get('users');

        $serviceData = (object)$usersRepository->getById($id);

        if (isset($serviceData->scalar) === true && $serviceData->scalar === false) {
            throw new NotFoundException('Não foi encontrado nenhum serviço com esse id');
        }

        $service = new ServiceResource;

        $service
            ->setId($serviceData->id)
            ->setName($serviceData->name)
            ->setDescription($serviceData->description)
            ->setCategorieId($serviceData->categorie_id)
            ->setPrice($serviceData->price)
            ->setCreatedAt(new DateTimeImmutable($serviceData->created_at));

        return $service;
    }

    /**
     * @return ServiceResource[]
     */
    public function search(array $params): array
    {
        $usersRepository = $this->repository->get('users');

        $usersData = $usersRepository->search($params);

        $users = [];

        if (count($usersData) === 0) {
            throw new NotFoundException('Não foi encontrado nenhum serviço');
        }

        return $users;
    }

    public function validationUser(object $user): bool
    {
        try {
            $serviceValidator = v::attribute('user_name', v::stringType()->notEmpty())
                ->attribute('user_email', v::stringType()->notEmpty()->email())
                ->attribute('user_password', v::stringType()->notEmpty())
                ->attribute('user_type_id', v::IntType()->notEmpty())
                ->attribute('created_at', v::dateTime('Y-m-d H:i:s'));

            $serviceValidator->assert($user);

            return true;
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ValidationErrorException(json_encode($e->getMessages()));
        }
    }
}
