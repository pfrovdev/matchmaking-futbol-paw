<?php
namespace Paw\App\Controllers;

use Paw\App\Commons\NotificadorEmail;
use Paw\App\Models\Equipo;
use Paw\App\Models\EquipoCollection;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

class DesafioController extends AbstractController{

    public function aceptDesafio(){
        $equipo_jwt_data = AuthMiddelware::verificarRoles(['USUARIO']);
        $equipo = $this->getEquipo($equipo_jwt_data->id_equipo);
        
        $equipoId  = (int) ($_POST['id_equipo']  ?? 0);
        $desafioId = (int) ($_POST['id_desafio'] ?? 0);

        if($equipo->fields['id_equipo'] != $equipoId){
            // implementar logica
            echo "no coinciden los ids";
        }

        $desafioAceptado = $equipo->aceptarDesafio($desafioId);
        $equipoDesafiante = $desafioAceptado->getEquipoDesafiante();

        $notificador = new NotificadorEmail();
        $notificador->enviarNotificacionDesafioAceptado($equipo, $equipoDesafiante, $desafioAceptado);

        header("Location: /dashboard");
    }

    public function rejectDesafio(){
        $equipo_jwt_data = AuthMiddelware::verificarRoles(['USUARIO']);
        $equipo = $this->getEquipo($equipo_jwt_data->id_equipo);
        
        $equipoId  = (int) ($_POST['id_equipo']  ?? 0);
        $desafioId = (int) ($_POST['id_desafio'] ?? 0);

        if($equipo->fields['id_equipo'] != $equipoId){
            // implementar logica
            echo "no coinciden los ids";
        }

        $desafioRechazado = $equipo->rechazarDesafio($desafioId);
        $equipoDesafiante = $desafioRechazado->getEquipoDesafiante();

        $notificador = new NotificadorEmail();
        $notificador->enviarNotificacionDesafioRechazado($equipo, $equipoDesafiante, $desafioRechazado);

        header("Location: /dashboard");
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
