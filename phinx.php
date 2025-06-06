<?php

require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'production_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => getenv("DB_ADAPTER") ?? 'mysql',
            'host' => getEnv("DB_DBHOST") ?? 'localhost',
            'name' => getenv("DB_DBNAME") ?? 'development_db',
            'user' => getenv("DB_USERNAME") ?? 'root',
            'pass' => getenv("DB_PASSWORD") ?? '',
            'port' => getenv("DB_PORT") ?? '3306',
            'charset' => getenv("DB_CHARSET") ?? 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
