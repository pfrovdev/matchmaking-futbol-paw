<?php

namespace Paw\Core\Database;

use Paw\Core\Traits\Loggeable;
use PDO;
use Monolog\Logger;

class QueryBuilder
{
    use Loggeable;
    protected $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function select($table)
    {
        $query = "SELECT * FROM {$table}";
        $sentencia = $this->pdo->prepare($query);
        $sentencia->setFetchMode(PDO::FETCH_ASSOC);
        $sentencia->execute();
        return $sentencia->fetchAll();
    }

    public function insert(){}

    public function update(){}

    public function delete(){}
}

?>