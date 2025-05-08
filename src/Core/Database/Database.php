<?php

namespace Paw\Core\Database;

use PDO;
use Paw\Core\Traits\Loggeable;
use Monolog\Logger;
class Database
{
    use Loggeable;

    private static ?PDO $connection = null;

    public static function initialize(array $config, Logger $log ): void
    {
        if (self::$connection === null) {
            $connectionBuilder = new ConnectionBuilder();
            if (method_exists($connectionBuilder, 'setLogger')) {
                $connectionBuilder->setLogger($log);
            }
            self::$connection = $connectionBuilder->make($config);
        }
    }
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            throw new \Exception("Database connection has not been initialized.");
        }
        return self::$connection;
    }
}
