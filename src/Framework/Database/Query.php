<?php

namespace Framework\Database;

use PDO;

class Query
{
    private $select;

    private $from;

    private $where = [];

    private $group;

    private $order;

    private $limit;

    private $params;

    private $entity;

    /**
     * @var null|PDO
     */
    private $pdo;

    public function __construct(? Pdo $pdo = null)
    {

        $this->pdo = $pdo;
    }

    public function from(string $table, ? string $alias = null): self
    {
        if ($alias) {
            $this->from[$alias] = $table;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    public function where(string ... $conditions): self
    {
        foreach ($conditions as $condition) {
            if (!array_search($condition, $this->where)) {
                $this->where[] = $condition;
            }
        }
        if (empty($this->where)) {
            $this->where = array_merge($this->where, $conditions);
        }
        return $this;
    }

    public function count(): int
    {
        $this->select("COUNT(id)");
        return $this->execute()->fetchColumn();
    }

    public function select(string ...$fields): self
    {
        $this->select = $fields;
        return $this;
    }

    private function execute()
    {
        $query = $this->__toString();
        if ($this->params) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pdo->query($query);
    }

    public function __toString()
    {
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = "*";
        }
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->where)) {
            $parts[] = "WHERE";
            $parts[] = "(" . join(') AND (', $this->where) . ')';
        }
        return join(' ', $parts);
    }

    private function buildFrom()
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$value AS $key";
            } else {
                $from[] = $value;
            }
        }
        return join(", ", $from);
    }

    public function params(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    public function all(): QueryResult
    {
        return new QueryResult(
            $this->execute()->fetchAll(PDO::FETCH_ASSOC),
            $this->entity
        );
    }
}
