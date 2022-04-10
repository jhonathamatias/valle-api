<?php

namespace Valle\Models;

use DateTimeImmutable;
use Valle\Factorys\RepositoryFactory;
use Valle\Models\Exceptions\NotFoundException;
use Valle\Resource\Service as ServiceResource;

class UsersRoles
{
    public function __construct(protected RepositoryFactory $repository)
    {
    }

    public function roles(int $userId): mixed
    {
        $roles = $this->repository->get()->query()
            ->select(
                'rol.name as role_name',
                'rol.description as role_description'
            )->from('roles', 'rol')
                ->innerJoin('rol', 'users_roles', 'us_rol', 'rol.id=us_rol.role_id')
            ->where('us_rol.user_id = ?')
            ->setParameter(0, $userId)
            ->executeQuery()
        ->fetchOne();

        if ($roles === false) {
            throw new NotFoundException('Não foi encontrado nenhuma regra');
        }

        return $roles;
    }
}
