<?php namespace Valle\Factorys;

use Valle\Libs\Repository;

class RepositoryFactory
{
    protected $register;

    public function __construct($register)
    {
        $this->register = $register;
    }

    public function get(string $repository = ''): Repository
    {
        return new Repository($repository, $this->register);
    }

    public function getRegister()
    {
        return $this->register;
    }
}