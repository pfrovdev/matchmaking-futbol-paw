<?php

namespace Paw\Core\Database;

use PDO;
use PDOException;
use Paw\Core\Traits\Loggeable;

class ConnectionBuilder
{
    # Herencia de loggers
    use Loggeable;

    public function make(array $config): PDO {

        try {
            $adapter = $config['DB_ADAPTER'];
            $host = $config['DB_HOSTNAME'];
            $dbname = $config['DB_DATABASE'];
            $username = $config['DB_USERNAME'];
            $password = $config['DB_PASSWORD'];
            $port = $config['DB_PORT'];
            $charset = $config['DB_CHARSET'];

            $dsn = "{$adapter}:host={$host};dbname={$dbname};port={$port};charset={$charset}";

            $this->logger->info("Conectando a la base de datos en {$host}:{$port}/{$dbname} con el usuario {$username}");
    
            return new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            $this->logger->error("Error al conectar a la base de datos: {$e->getMessage()}");
            die("Error al conectar a la base de datos");
        }
    }

}

?>