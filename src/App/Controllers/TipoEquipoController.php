<?php

namespace Paw\App\Controllers;

use Paw\Core\AbstractController;
use Paw\App\Models\TipoEquipo;

class TipoEquipoController extends AbstractController{

    public ?string $modelName = TipoEquipo::class;
    
    public function get_estado($tipo) {
        $model_type = $this->model->get_estado($tipo);
        if(is_null($model_type)){
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }
        
    }

}

?>