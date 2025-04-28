<?php

namespace Paw\Core;

use Paw\Core\AbstractModel;
use Paw\Core\Database\QueryBuilder;

class AbstractController{
    public string $viewsDir = "";

    public ?string $modelName = null;
    public ?object $model = null;

    public function __construct(){
        global $connection, $log;
        $this->viewsDir = __DIR__ . "/../App/views/";

        if(!is_null($this->modelName)){
            //$qb = new QueryBuilder($connection, $log);
            $model = new $this->modelName;
            //$model->setQueryBuilder($qb);
            $this->setModel($model);
        }
    }

    public function setModel(?object $model){
        $this->model = $model;
    }

}

?>