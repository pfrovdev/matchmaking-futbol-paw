<?php
namespace Paw\Core;
use Paw\Core\ModelFactory;
use Monolog\Logger;
use Paw\Core\Middelware\AuthMiddelware;

class AbstractController{
    public string $viewsDir = "";

    public ?string $modelName = null;
    public ?object $model = null;
    protected ModelFactory $modelFactory;
    protected Logger $logger;
    protected AuthMiddelware $auth;

    public function __construct(Logger $log){
        $this->logger = $log;
        $this->viewsDir = __DIR__ . "/../App/views/";
        $this->modelFactory = new ModelFactory($log);
        $this->auth = new AuthMiddelware();

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