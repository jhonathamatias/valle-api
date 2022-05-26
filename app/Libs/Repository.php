<?php namespace Valle\Libs;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Valle\Interfaces\MapperSearchInterface;
use Valle\Mappers\Search;

class Repository
{
    /**
     * @var string
     */
    protected $repository;

    /**
     * @var Connection
     */
    protected $register;

    /**
     * @var int
     */
    protected $lastInsertId;

    public function __construct(string $repository, Connection $register)
    {
        $this->repository = $repository;
        $this->register = $register;
    }

    public function setRepository(string $repository)
    {
        $this->repository = $repository;
    }

    public function executeQuery(string $query, array $fields)
    {
        return $this->register->executeQuery($query, $fields);
    }

    public function getById(int $id, string $fields="*")
    {
        $statement = $this->register->executeQuery("SELECT $fields FROM $this->repository WHERE id=?", [$id]);

        return $statement->fetchAssociative();
    }

    public function getAll(string $fields="*", string $where="", array $values=[])
    {
        $statement = $this->register->executeQuery("SELECT $fields FROM $this->repository $where", $values);

        return $statement->fetchAllAssociative();
    }

    public function search(array $params, string $fields="*", $limit=100, $offset=0): MapperSearchInterface
    {
        $limit = isset($params['limit']) ? $params['limit'] : $limit;
        $offset = isset($params['offset']) ? $params['offset'] : $offset;

        unset($params['limit']);
        unset($params['offset']);

        $where = "WHERE " . implode(" LIKE ? AND ", array_keys($params));
        $values = array_values($params);
        $newValues = [];

        foreach ($values as $value) {
            $newValues[] = "%$value%";
        }

        $where .=  " LIKE ?";

        if (count($params) === 0) {
            $where = "";
        }

        $sql = "SELECT $fields FROM $this->repository $where LIMIT $limit OFFSET $offset";

        $statement = $this->register->executeQuery($sql, $newValues);
        
        return new Search((array)$statement->fetchAllAssociative(), $limit, $offset, $statement->rowCount());
    }

    /**
     * @param string $where example: id=?
     * @param array $values example: [$id]
     * @param array $fields example: name, email
     */
    public function where(string $where, array $values, string $fields="*")
    {
        $statement = $this->register->executeQuery("SELECT $fields FROM $this->repository WHERE $where", $values);
        
        return $statement->fetchAllAssociative();
    }

    public function getLastInsertId()
    {
        return $this->register->lastInsertId();
    }

    public function insert(array $fields)
    {
        $affectedRows = $this->register->insert($this->repository, $fields);

        $this->lastInsertId = $this->getLastInsertId();

        return $affectedRows;
    }

    public function updateEquals(array $fields, array $whereEquals)
    {
        return $this->register->update($this->repository, $fields, $whereEquals);
    }

    public function query(): QueryBuilder
    {
        return $this->register->createQueryBuilder();
    }
}