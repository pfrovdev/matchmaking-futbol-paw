<?php

namespace Paw\App\Dtos;

class BadgeEquipoFormularoDto
{
    public string $nombre_equipo;
    public string $acronimo;
    public string $descripcion_elo;

    public function __construct(string $nombre_equipo, string $acronimo, string $descripcion_elo)
    {
        $this->nombre_equipo = $nombre_equipo;
        $this->acronimo = $acronimo;
        $this->descripcion_elo = $descripcion_elo;
    }

    public function getNombreEquipo(): string
    {
        return $this->nombre_equipo;
    }

    public function getAcronimo(): string
    {
        return $this->acronimo;
    }

    public function getDescripcionElo(): string
    {
        return $this->descripcion_elo;
    }
}
