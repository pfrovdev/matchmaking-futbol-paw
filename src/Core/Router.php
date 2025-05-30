<?php

namespace Paw\Core;

use Exception;
use Paw\Core\Exceptions\RouteNotFoundException;
use Paw\Core\Request;
use Paw\Core\Traits\Loggeable;

class Router {

    use Loggeable;
    public array $routes = [
        "GET" => [],
        "POST" => [],
        "PUT" => [],
        "DELETE" => []
    ];

    public string $notFound = "not_found";
    public string $internalError = "internal_error";
    private Container $container;

    public function __construct()
    {
        $this->get($this->notFound, 'ErrorController@notFound');
        $this->get($this->internalError, 'ErrorController@internalError');
    }

    public function loadRoutes($path, $action, $method = "GET")
    {
        $this->routes[$method][$path] = $action;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function get($path, $action)
    {
        $this->routes["GET"][$path] = $action;
    }

    public function post($path, $action)
    {
        $this->routes["POST"][$path] = $action;
    }

    public function put($path, $action)
    {
        $this->routes["PUT"][$path] = $action;
    }

    public function delete($path, $action)
    {
        $this->routes["DELETE"][$path] = $action;
    }

    public function exists($path, $method)
    {
        return array_key_exists($path, $this->routes[$method]);
    }

    public function getController($path, $http_method)
    {
        if (!array_key_exists($path, $this->routes[$http_method])) {
            throw new RouteNotFoundException("No existe ruta para esta Path y método HTTP");
        }
        return explode("@", $this->routes[$http_method][$path]);
    }

    public function call($controller, $method)
    {
        $controller_name = "Paw\\App\\Controllers\\{$controller}";
        $objController = $this->container->get($controller_name);
        $this->logger->info("Llamando al controlador: {$controller} y método: {$method}");
        $objController->$method();
    }

    public function direct(Request $request)
    {
        $this->logger->info("Ruta: {$request->uri()} y método HTTP: {$request->method()}");

        try {
            $route = $request->route();
            $path = $route['uri'];
            $http_method = $route['method'];
            list($controller, $method) = $this->getController("/" . $path, $http_method);
        } catch (RouteNotFoundException $e) {
            $this->logger->error("Ruta no encontrada: " . $e->getMessage());
            list($controller, $method) = $this->getController($this->notFound, "GET");
        } catch (Exception $e) {
            $this->logger->error("Error: {$e->getMessage()}");
            list($controller, $method) = $this->getController($this->internalError, "GET");
            $this->call($controller, $method);
        } finally {
            $this->call($controller, $method);
        }
    }
}