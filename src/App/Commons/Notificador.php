<?php 

namespace Paw\App\Commons;

use Paw\App\Models\Comentario;
use Paw\App\Models\Desafio;
use Paw\App\Models\Equipo;
use Paw\App\Models\FormularioPartido;

interface Notificador{
    function enviarNotificacionDesafioAceptado(Equipo $equipo, Equipo $equipoDesafiante, Desafio $desafioCreado) : void;
    function enviarNotificacionDesafioRechazado(Equipo $equipoDesafiado, Equipo $equipoDesafiante, Desafio $desafioRechazado): void;

    function enviarNotificacionPartidoFinalizado(Equipo $equipoLocal, Equipo $equipoVisitante, FormularioPartido $formularioLocal, FormularioPartido $formularioVisitante): void;

    function enviarNotificacionPartidoNoAcordado(Equipo $equipoLocal, Equipo $equipoVisitante): void;

    function enviarNotificacionNuevaIteracion(Equipo $equipoLocal, Equipo $equipoVisitante, int $iteracion, int $idPartido);

    function enviarNotificacionComentarioEquipo(Equipo $equipoComentado, Equipo $equipoComentador, Comentario $comentario);

    function enviarNotificacionPartidoCancelado(Equipo $equipoQueCancela, Equipo $equipoDebeSerNotificado): void;
}


?>