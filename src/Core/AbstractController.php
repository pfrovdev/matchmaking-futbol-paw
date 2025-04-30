<?php
namespace Paw\Core;
use Paw\Core\ModelFactory;
use Monolog\Logger;

class AbstractController{
    public string $viewsDir = "";

    public ?string $modelName = null;
    public ?object $model = null;
    protected ModelFactory $modelFactory;

    public function __construct(Logger $log){
        $this->viewsDir = __DIR__ . "/../App/views/";
        $this->modelFactory = new ModelFactory($log);

        if(!is_null($this->modelName)){
            $this->model = $this->modelFactory->make($this->modelName);
        }
    }
    public function getModel(string $modelClass): object
    {
        return $this->modelFactory->make($modelClass);
    }
}
?>