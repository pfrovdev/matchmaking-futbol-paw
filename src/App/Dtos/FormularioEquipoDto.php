<?php

namespace Paw\App\Dtos;

class FormularioEquipoDto
{
    public badgeEquipoFormularoDto $badge;
    public int $goles;
    public int $asistencias;
    public string $tarjetas_amarilla;
    public string $tarjetas_roja;

    public function __construct(badgeEquipoFormularoDto $badge, int $goles, int $asistencias, string $tarjetas_amarilla, string $tarjetas_roja)
    {
        $this->goles = $goles;
        $this->asistencias = $asistencias;
        $this->tarjetas_amarilla = $tarjetas_amarilla;
        $this->tarjetas_roja = $tarjetas_roja;
        $this->badge = $badge;
    }

    public function getGoles(): int
    {
        return $this->goles;
    }

    public function getAsistencias(): int
    {
        return $this->asistencias;
    }

    public function getTarjetasAmarilla(): string
    {
        return $this->tarjetas_amarilla;
    }

    public function getTarjetasRoja(): string
    {
        return $this->tarjetas_roja;
    }

    public function getBadge() : badgeEquipoFormularoDto
    {
        return $this->badge;
    }

}