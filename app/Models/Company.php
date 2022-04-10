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

class Company
{
    public function __construct(protected RepositoryFactory $repository)
    {
    }

    public function create(object $post): int
    {
        $companyRepository = $this->repository->get('company');

        $company = (object)$post->company;

        $company->created_at = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        if ($this->alreadyExists($company->document) === true) {
            throw new AlreadyExistsException('Já existe uma empresa com esse documento.');
        }

        $this->validationCompany($company);

        $companyRepository->insert([
            'name'       => $company->name,
            'email'      => $company->email,
            'phone'      => $company->phone,
            'document'   => $company->document,
            'created_at' => $company->created_at
        ]);

        $id = $companyRepository->getLastInsertId();

        if ((bool)$id === false) {
            throw new ErrorInsertException('Erro ao adicionar empresa');
        }

        return $id;
    }

    /**
     * @return array
     */
    public function search(array $params = []): array
    {
        $companyRepository = $this->repository->get('company');

        $companyData = $companyRepository->search($params);

        if (count($companyData) === 0) {
            throw new NotFoundException('Não foi encontrado nenhuma empresa');
        }

        return $companyData;
    }

    public function alreadyExists(string $document): bool
    {
        $companyRepository = $this->repository->get('company');

        $company = $companyRepository->where('document = ?', [$document])[0] ?? null;

        return $company !== null;
    }

    public function validationCompany(object $company): bool
    {
        try {
            $companyValidator = v::attribute('name', v::stringType()->notEmpty())
                ->attribute('email', v::stringType()->notEmpty()->email())
                ->attribute('phone', v::stringType()->notEmpty())
                ->attribute('document', v::stringType()->notEmpty()->cnpj())
                ->attribute('created_at', v::dateTime('Y-m-d H:i:s'));

            $companyValidator->assert($company);

            return true;
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ValidationErrorException(json_encode($e->getMessages()));
        }
    }
}
