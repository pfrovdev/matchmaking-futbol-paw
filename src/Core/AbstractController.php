<?php
namespace Paw\Core;

use Monolog\Logger;

use Paw\Core\Database\QueryBuilder;

class AbstractController{
    public string $viewsDir = "";

    public ?string $modelName = null;
    public ?object $model = null;

    public function __construct(Logger $log){
        $this->viewsDir = __DIR__ . "/../App/views/";

        if(!is_null($this->modelName)){
            $queryBuilder = QueryBuilder::getInstance();
            $queryBuilder->setLogger($log);
            $model = new $this->modelName($queryBuilder);
            $this->setModel($model);
        }
    }

    public function setModel(?object $model){
        $this->model = $model;
    }
}
?>