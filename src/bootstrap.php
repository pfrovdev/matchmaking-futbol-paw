<?php
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Paw\Core\Container;
use Paw\Core\ContainerConfig;
use Paw\Core\Router;
use Paw\Core\Request;
// use Paw\Core\Database\ConnectionBuilder;
use Paw\Core\Database\Database;

// Cargamos configuración
$config = require __DIR__ . '/../src/Config/config.php';
define('DEBUG', $config['debug']);

// Logger
$log = new Logger($config['log']['name']);
$log->pushHandler(new StreamHandler($config['log']['path'], $config['log']['level']));

// Inicializamos la base de datos
Database::initialize($config['database'], $log );

// Whoops (sólo en modo desarrollo)
if (DEBUG) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}


/* conexión para proxima entrega
$connectionBuilder = new ConnectionBuilder;
$connectionBuilder->setLogger($log);
$connection = $connectionBuilder->make($config['database']);
*/

$request = new Request;

// Instancio el contenedor para inyectar las dependencias
$container = new Container();
// se las inyecto
ContainerConfig::configure($container);

// Cargamos rutas desde config
$router = new Router();
$router->setLogger($log);
$router->setContainer($container);

foreach ($config['routes'] as $route) {
    $router->loadRoutes($route['path'], $route['action'], $route['method']);
}

