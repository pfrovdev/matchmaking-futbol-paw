<?php
namespace Paw\App\Controllers;

use Paw\App\Models\Equipo;
use Paw\App\Models\EquipoCollection;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

class DesafioController extends AbstractController{

    public function aceptDesafio(int $equipoId, int $desafioId){
        $equipo_jwt_data = AuthMiddelware::verificarRoles(['USUARIO']);
        $equipo = $this->getEquipo($equipo_jwt_data->id_equipo);

        if($equipo->fields['id_equipo'] != $equipoId){
            // implementar logica
            echo "no coinciden los ids";
        }

        $equipo->aceptarDesafio($desafioId);
        require $this->viewsDir . 'home.php';
    }

    public function rejectDesafio(int $equipoId, int $desafioId){

        require $this->viewsDir . 'home.php';
    }

    private function getEquipo(int $id_equipo): Equipo {

        $equipoCollection = $this->getModel(EquipoCollection::class);

        $equipo_data_bd = $equipoCollection->getById($id_equipo)[0];

        $equipo = $this->getModel(Equipo::class);

        $equipo->set($equipo_data_bd);

        return $equipo;
    }

}
?>
