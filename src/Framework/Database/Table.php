<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;
use PDO;

class Table
{
    /**
     * Name of the table in BDD
     * @var string
     */
    protected $table;
    /**
     * Entity to use
     * @var string|null
     */
    protected $entity;
    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct(\PDO $PDO)
    {
        $this->pdo = $PDO;
    }

    /**
     * Paginate
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function paginationQuery()
    {
        return "SELECT * FROM {$this->table}";
    }

    /**
     * get list Key - Value of entries
     */
    public function findList(): array
    {
        $results = $this->pdo->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);

        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Find element by id
     *
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ? ", [$id]);
    }

    /**
     * Execute query, and get first result
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * @return array
     * @throws NoRecordException
     */
    public function findAll(): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table}");
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        $record = $query->fetchAll();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
    }

    /**
     * Update a entry
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    /**
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * Create an entry
     *
     * @param $params
     * @return id|null|string
     */
    public function insert($params)
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $statement = $this->pdo->prepare("INSERT INTO {$this->table}  SET $fieldQuery");
        $executed = $statement->execute($params);
        return $executed ? $this->pdo->lastInsertId() : null;
    }

    /**
     * Delete an entry
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table}  WHERE id = ?");
        return $statement->execute([$id]);
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Check if element exists
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $statement = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id= ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }

    /**
     * count entries
     * @return int
     */
    public function count(): int
    {
        return $this->fethColumn("SELECT count(id) FROM {$this->table}");
    }

    /**
     * get first column
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function fethColumn(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchColumn();
    }
}
