<?php

namespace Paw\Core\Database;

use Paw\Core\Traits\Loggeable;
use PDO;

class QueryBuilder
{
    use Loggeable;
    private static ?self $instance = null;
    private PDO $pdo;
    private function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    public static function getInstance(): self{
        if (self::$instance === null) {
            $pdo = Database::getConnection();
            self::$instance = new self($pdo);
        }
        return self::$instance;
    }
    // public function select($table){
    //     $query = "SELECT * FROM {$table}";
    //     $sentencia = $this->pdo->prepare($query);
    //     $sentencia->setFetchMode(PDO::FETCH_ASSOC);
    //     $sentencia->execute();
    //     return $sentencia->fetchAll();
    // }

    public function select($table, array $params = []){
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

        $statement = $this->pdo->prepare($query);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $statement->execute($values);

        return $statement->fetchAll();
    }

    public function insert(string $table, array $values): ?string{
        if (empty($values)) {
            throw new \InvalidArgumentException('No se proporcionaron valores para insertar.');
        }

        $columns = implode(", ", array_keys($values));
        $placeholders = implode(", ", array_fill(0, count($values), '?'));

        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute(array_values($values));

            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            $this->logger->error("Error al insertar en {$table}: " . $e->getMessage());
            return null;
        }
    }


    public function update(){}

    public function delete(){}
}

?>