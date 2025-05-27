<?php

namespace Paw\App\Services;

use Paw\App\Commons\Notificador;
use Paw\App\Models\Equipo;
use Paw\App\Models\Desafio;

class NotificationService
{
    private Notificador $notificador;

    public function __construct(Notificador $notificador)
    {
        $this->notificador = $notificador;
    }

    public function notifyDesafioCreated(Equipo $desafiante, Equipo $desafiado, Desafio $desafio
    ): void {
        $this->notificador->enviarNotificacionDesafioAceptado(
            $desafiante,
            $desafiado,
            $desafio
        );
    }

    public function notifyDesafioAccepted(Equipo $aceptante, Equipo $desafiante, Desafio $desafio
    ): void {
        $this->notificador->enviarNotificacionDesafioAceptado(
            $aceptante,
            $desafiante,
            $desafio
        );
    }

    public function notifyDesafioRejected(Equipo $desafiado, Equipo $desafiante, Desafio $desafio
    ): void {
        $this->notificador->enviarNotificacionDesafioRechazado(
            $desafiado,
            $desafiante,
            $desafio
        );
    }
}
