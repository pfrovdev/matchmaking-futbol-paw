<?php 

namespace Paw\App\Commons;

use Paw\App\Models\Desafio;
use Paw\App\Models\Equipo;

interface Notificador{
    function enviarNotificacionDesafioAceptado(Equipo $equipo, Equipo $equipoDesafiante, Desafio $desafioCreado) : void;
    function enviarNotificacionDesafioRechazado(Equipo $equipoDesafiado, Equipo $equipoDesafiante, Desafio $desafioRechazado): void;

}


?>