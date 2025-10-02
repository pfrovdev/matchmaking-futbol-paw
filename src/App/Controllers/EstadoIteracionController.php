<?php

namespace Paw\App\Controllers;

use Paw\Core\AbstractController;
use Paw\App\Models\EstadoIteracion;

class EstadoIteracionController extends AbstractController{

    public ?string $modelName = EstadoIteracion::class;
    
    public function get_estado($estado) {
        $model_state = $this->model->get_estado($estado);
        if(is_null($model_state)){
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }
        
    }

}

?>