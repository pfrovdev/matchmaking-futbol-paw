<?php

namespace Paw\App\Controllers;

use Paw\Core\AbstractController;
use Paw\App\Models\EstadoIteracion;

class EstadoIteracionController extends AbstractController{

    public ?string $modelName = EstadoIteracion::class;
    
    public function get_estado($estado) {
        $model_state = $this->model->get_estado($estado);
        if(is_null($model_state)){
            require $this->viewsDir . 'not-found.php';
            exit;
        }
        
    }

}

?>