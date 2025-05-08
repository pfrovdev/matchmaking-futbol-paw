<?php

namespace Paw\App\Controllers;

use Paw\Core\AbstractController;
use Paw\App\Models\EstadoPartido;

class EstadoPartidoController extends AbstractController{

    public ?string $modelName = EstadoPartido::class;
    
    public function get_estado($estado) {
        $model_state = $this->model->get_estado($estado);
        if(is_null($model_state)){
            require $this->viewsDir . 'not-found.php';
            exit;
        }
        
    }

}

?>