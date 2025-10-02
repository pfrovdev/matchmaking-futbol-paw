<?php

namespace Paw\App\Services;

use Paw\App\Commons\Notificador;
use Paw\App\Models\Comentario;
use Paw\App\Models\Equipo;
use Paw\App\Models\Desafio;
use Paw\App\Models\FormularioPartido;

class NotificationService
{
    private Notificador $notificador;

    public function __construct(Notificador $notificador)
    {
        $this->notificador = $notificador;
    }

    public function notifyDesafioCreated(
        Equipo $desafiante,
        Equipo $desafiado
    ): void {
        $this->notificador->enviarNotificacionDesafioCreado(
            $desafiante,
            $desafiado
        );
    }

    public function notifyDesafioAccepted(
        Equipo $aceptante,
        Equipo $desafiante,
        Desafio $desafio
    ): void {
        $this->notificador->enviarNotificacionDesafioAceptado(
            $aceptante,
            $desafiante,
            $desafio
        );
    }

    public function notifyDesafioRejected(
        Equipo $desafiado,
        Equipo $desafiante,
        Desafio $desafio
    ): void {
        $this->notificador->enviarNotificacionDesafioRechazado(
            $desafiado,
            $desafiante,
            $desafio
        );
    }

    public function notifyParitdoFinalizado(Equipo $equipoLocal, Equipo $equipoVisitante, FormularioPartido $formularioLocal, FormularioPartido $formularioVisitante)
    {
        $this->notificador->enviarNotificacionPartidoFinalizado(
            $equipoLocal,
            $equipoVisitante,
            $formularioLocal,
            $formularioVisitante
        );
    }

    public function notifyParitdoCancelado(
        Equipo $equipoQueCancela,
        Equipo $equipoDebeSerNotificado
    ) {
        $this->notificador->enviarNotificacionPartidoCancelado(
            $equipoQueCancela,
            $equipoDebeSerNotificado,
        );
    }

    public function notifyParitdoNoAcordado(Equipo $equipoLocal, Equipo $equipoVisitante)
    {
        $this->notificador->enviarNotificacionPartidoNoAcordado(
            $equipoLocal,
            $equipoVisitante
        );
    }

    public function notifyNuevaIteracion(Equipo $equipoLocal, Equipo $equipoVisitante, int $iteracionActual, int $idPartido)
    {
        $this->notificador->enviarNotificacionNuevaIteracion(
            $equipoLocal,
            $equipoVisitante,
            $iteracionActual,
            $idPartido
        );
    }

    public function notifyEquipoComentado(Equipo $equipoComentado, Equipo $equipoComentador, Comentario $comentario): void
    {
        $this->notificador->enviarNotificacionComentarioEquipo(
            $equipoComentado,
            $equipoComentador,
            $comentario
        );
    }
}
