<?php
/**
 * Script para limpiar (borrar todos los registros) de las tablas de la base de datos.
 *
 * USO:
 *   php src/Deploy_database/clean_db.php
 *
 * Este script elimina los datos de todas las tablas especificadas en $orderedTables.
 * Se recomienda ejecutarlo antes de volver a correr insert_demo_data.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Core/Database/Database.php';
require_once __DIR__ . '/../Core/Database/ConnectionBuilder.php';

$config = require __DIR__ . '/../../src/Config/config.php';

use Paw\Core\Database\Database;
echo "DB_PASSWORD=[" . getenv('DB_PASSWORD') . "]\n";
Database::initialize($config['database'], new \Monolog\Logger('db'));
$pdo = Database::getConnection();

$tables = [
    'Estadisticas',
    'ResultadoPartido',
    'FormularioPartido',
    'Desafio',
    'Partido',
    'Comentario',
    'Equipo'
];

// Desactivar claves foráneas
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

foreach ($tables as $table) {
    try {
        echo "Limpiando tabla '$table'...\n";
        $pdo->exec("TRUNCATE TABLE `$table`");
        echo "Tabla '$table' limpiada.\n";
    } catch (PDOException $e) {
        echo "Error al limpiar tabla $table: " . $e->getMessage() . "\n";
    }
}

// Reactivar claves foráneas
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "Base de datos limpiada completamente.\n";