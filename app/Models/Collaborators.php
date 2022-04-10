<?php

namespace Valle\Models;

use DateTimeImmutable;
use Valle\Factorys\RepositoryFactory;
use Valle\Models\Exceptions\AlreadyExistsException;
use Valle\Models\Exceptions\ErrorInsertException;
use Valle\Models\Exceptions\NotFoundException;
use Valle\Models\Exceptions\ValidationErrorException;
use Respect\Validation\Validator as v;

class Collaborators
{
    public function __construct(protected RepositoryFactory $repository)
    {
    }

    public function create(object $post): int
    {
        $collaboratorRepository = $this->repository->get('collaborators');

        $collaborator = (object)$post->collaborator;

        $collaborator->created_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        if ($this->alreadyExists((int)$collaborator->user_id) === true) {
            throw new AlreadyExistsException('Já existe um colaborador vinculado a esse usúario.');
        }

        $this->validationCollaborator($collaborator);

        $collaboratorRepository->insert([
            'user_id'       => $collaborator->user_id,
            'occupation_id' => $collaborator->occupation_id,
            'created_at'    => $collaborator->created_at
        ]);

        $id = $collaboratorRepository->getLastInsertId();

        if ((bool)$id === false) {
            throw new ErrorInsertException('Erro ao criar colaborador');
        }

        return $id;
    }

    /**
     * @return array
     */
    public function search(array $params = []): array
    {
        $repository = $this->repository->get('colaborators');

        $data = $repository->search($params);

        if (count($data) === 0) {
            throw new NotFoundException('Não foi encontrado colaborador');
        }

        return $data;
    }

    public function getOccupation(): array
    {
        $repository = $this->repository->get('collaborators_occupation');

        $data = $repository->getAll();

        if (count($data) === 0) {
            throw new NotFoundException('Não foi encontrado nenhum cargo');
        }

        return $data;
    }

    public function alreadyExists(int $userId): bool
    {
        $repository = $this->repository->get('collaborators');

        $collaborator = $repository->where('user_id = ?', [$userId])[0] ?? null;

        return $collaborator !== null;
    }

    public function validationCollaborator(object $collaborator): bool
    {
        try {
            $validator = v::attribute('user_id', v::intType()->notEmpty())
                ->attribute('occupation_id', v::intType()->notEmpty())
                ->attribute('created_at', v::dateTime('Y-m-d H:i:s'));

            $validator->assert($collaborator);

            return true;
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ValidationErrorException(json_encode($e->getMessages()));
        }
    }
}
