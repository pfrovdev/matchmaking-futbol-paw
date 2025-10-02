<?php

namespace Paw\Core\Database;

use Paw\Core\Traits\Loggeable;
use PDO;

class QueryBuilder
{
    use Loggeable;
    private static ?self $instance = null;
    private PDO $pdo;
    private function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            $pdo = Database::getConnection();
            self::$instance = new self($pdo);
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function select(
        $table,
        array $params = [],
        ?string $orderBy = null,
        ?string $direction = 'ASC',
        ?int $limit = null,
        ?int $offset = null
    ) {
        $query = "SELECT * FROM {$table}";
        $values = [];

        if (!empty($params)) {
            $conditions = [];
            foreach ($params as $field => $value) {
                $conditions[] = "$field = ?";
                $values[] = $value;
            }
            $query .= " WHERE " . implode(" AND ", $conditions);
        } else {
            $query .= " WHERE 1=1";
        }

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy} {$direction}";
        }

        if ($limit !== null) {
            $query .= " LIMIT {$limit}";
            if ($offset !== null) {
                $query .= " OFFSET {$offset}";
            }
        }

        $statement = $this->pdo->prepare($query);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $statement->execute($values);

        return $statement->fetchAll();
    }

    public function selectAdvanced(
        string $table,
        array $conditions = [],
        array $rawConditions = [],
        ?string $orderBy = null,
        ?string $direction = 'ASC'
    ): array {
        $query = "SELECT * FROM {$table}";
        $values = [];
        $where = [];

        foreach ($conditions as $field => $value) {
            if (is_array($value) && isset($value['operator'], $value['value'])) {
                $where[] = "{$field} {$value['operator']} ?";
                $values[] = $value['value'];
            } else {
                $where[] = "{$field} = ?";
                $values[] = $value;
            }
        }

        foreach ($rawConditions as $raw) {
            $where[] = $raw['sql'];
            foreach ($raw['params'] as $param) {
                $values[] = $param;
            }
        }

        if ($where) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy} {$direction}";
        }

        $statement = $this->pdo->prepare($query);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $statement->execute($values);

        return $statement->fetchAll();
    }

    public function selectLike(
        string $table,
        array $params = [],
        ?string $orderBy = null,
        ?string $direction = 'ASC',
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $query = "SELECT * FROM {$table}";
        $values = [];

        if (!empty($params)) {
            $conditions = [];
            foreach ($params as $field => $value) {
                $conditions[] = "$field LIKE ?";
                $values[] = '%' . $value . '%';
            }
            $query .= " WHERE " . implode(" AND ", $conditions);
        } else {
            $query .= " WHERE 1=1";
        }

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy} {$direction}";
        }
        if ($limit !== null) {
            $query .= " LIMIT {$limit}";
            if ($offset !== null) {
                $query .= " OFFSET {$offset}";
            }
        }

        $statement = $this->pdo->prepare($query);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $statement->execute($values);

        return $statement->fetchAll();
    }

    public function selectOr(
        string $table,
        array  $orFields,
        mixed  $value,
        ?string $orderBy = null,
        string  $direction = 'ASC',
        ?int    $limit = null,
        ?int    $offset = null
    ): array {

        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        $clauses = [];
        $values  = [];
        foreach ($orFields as $f) {
            $clauses[] = "{$f} = ?";
            $values[]  = $value;
        }
        $whereSql = '(' . implode(' OR ', $clauses) . ')';

        $sql = "SELECT  * FROM {$table} WHERE {$whereSql}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function insert(string $table, array $values): ?string
    {
        if (empty($values)) {
            throw new \InvalidArgumentException('No se proporcionaron valores para insertar.');
        }

        $sets = [];
        $params = [];
        foreach ($values as $column => $value) {
            if (is_string($value) && strpos($value, 'ST_GeomFromText(') === 0) {
                $sets[] = "`$column` = $value";
            } else {
                $paramKey = ":$column";
                $sets[] = "`$column` = $paramKey";
                $params[$paramKey] = $value;
            }
        }

        $setClause = implode(', ', $sets);
        $query = "INSERT INTO `$table` SET $setClause";

        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $placeholder => $val) {
                $stmt->bindValue($placeholder, $val);
            }
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            $this->logger->error("Error al insertar en $table: " . $e->getMessage());
            return null;
        }
    }



    public function update(string $table, array $values, array $where): int|bool
    {
        if (empty($values)) {
            throw new \InvalidArgumentException('No se proporcionaron valores para actualizar.');
        }
        if (empty($where)) {
            throw new \InvalidArgumentException('No se proporcionaron condiciones WHERE para la actualización.');
        }

        $sets   = [];
        $params = [];
        foreach ($values as $column => $value) {
            if (is_string($value) && strpos($value, 'ST_GeomFromText(') === 0) {
                $sets[] = "`$column` = $value";
            } else {
                $paramKey       = ":upd_$column";
                $sets[]         = "`$column` = $paramKey";
                $params[$paramKey] = $value;
            }
        }

        $wheres = [];
        foreach ($where as $column => $value) {
            $paramKey         = ":where_$column";
            $wheres[]         = "`$column` = $paramKey";
            $params[$paramKey] = $value;
        }

        $setClause   = implode(', ', $sets);
        $whereClause = implode(' AND ', $wheres);
        $sql         = "UPDATE `$table` SET $setClause WHERE $whereClause";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $placeholder => $val) {
                $stmt->bindValue($placeholder, $val);
            }
            $stmt->execute();

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            $this->logger->error("Error al actualizar en $table: " . $e->getMessage());
            return false;
        }
    }

    public function delete(string $table, array $where): bool
    {
        if (empty($where)) {
            throw new \InvalidArgumentException('No se proporcionaron condiciones WHERE para la eliminación.');
        }

        $conditions = [];
        $params = [];
        foreach ($where as $column => $value) {
            $paramKey = ":where_$column";
            $conditions[] = "`$column` = $paramKey";
            $params[$paramKey] = $value;
        }

        $whereClause = implode(' AND ', $conditions);
        $sql = "DELETE FROM `$table` WHERE $whereClause";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $placeholder => $val) {
                $stmt->bindValue($placeholder, $val);
            }
            return $stmt->execute();
        } catch (\PDOException $e) {
            $this->logger->error("Error al eliminar de $table: " . $e->getMessage());
            return false;
        }
    }
    public function count(string $table, array $params = []): int
    {
        $query  = "SELECT COUNT(*) AS total FROM {$table}";
        $values = [];

        if (!empty($params)) {
            $conditions = [];
            foreach ($params as $field => $value) {
                $conditions[] = "$field = ?";
                $values[]     = $value;
            }
            $query .= " WHERE " . implode(" AND ", $conditions);
        } else {
            $query .= " WHERE 1=1";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }
}
