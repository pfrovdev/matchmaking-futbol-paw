<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\Core\Database\QueryBuilder;

abstract class DataMapper
{
    protected QueryBuilder $qb;
    protected string $table;
    protected Logger $logger;

    public function __construct(QueryBuilder $qb, string $table , Logger $logger)
    {
        $this->qb = $qb;
        $this->table = $table;
        $this->logger = $logger;
    }

    public function findById(array $params)
    {
        $results = $this->qb->select($this->table, $params);
        return $results[0] ?? null;
    }

    public function findAll(): array
    {
        return $this->qb->select($this->table);
    }

    public function selectAdvanced(
        string $table,
        array $conditions = [],
        array $rawConditions = [],
        ?string $orderBy = null,
        ?string $direction = 'ASC'
    ): array {
        return $this->qb->selectAdvanced($this->table, $conditions, $rawConditions, $orderBy, $direction);
    }

    public function insert(array $data): ?string
    {
        return $this->qb->insert($this->table, $data);
    }

    public function update(array $data, array $where): int|bool
    {
        return $this->qb->update($this->table, $data, $where);
    }

    public function delete(array $where): int|bool
    {
        return $this->qb->delete($this->table, $where);
    }

    public function findBy(array $where): array
    {
        return $this->qb->select($this->table, $where);
    }

    abstract public function mapAll(array $rows): array;
    abstract public function map(array $row): object;

    public function getPdo(): \PDO
    {
        return $this->qb->getPdo();
    }
}
