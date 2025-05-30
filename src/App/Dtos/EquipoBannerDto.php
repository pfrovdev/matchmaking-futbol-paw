<?php

namespace Paw\App\Dtos;

use Paw\App\Models\Equipo;

class EquipoBannerDto
{
    public string $id_equipo;
    public string $nombre_equipo;
    public string $acronimo;
    public string $url_foto_perfil;
    public string $lema;
    public int $elo_actual;
    public string $descripcion_elo;
    public int $deportividad;
    public string $tipoEquipo;

    public function __construct(Equipo $equipo, string $descripcion_elo, int $deportividad, string $tipoEquipo)
    {
        $this->id_equipo = $equipo->getIdEquipo();
        $this->nombre_equipo = $equipo->getNombre();
        $this->acronimo = $equipo->getAcronimo();
        $this->url_foto_perfil = $equipo->getUrlFotoPerfil()?? '';
        $this->lema = $equipo->getLema();
        $this->elo_actual = $equipo->getEloActual();
        $this->descripcion_elo = $descripcion_elo;
        $this->deportividad = $deportividad;
        $this->tipoEquipo = $tipoEquipo;
    }

    public function getIdEquipo(): string
    {
        return $this->id_equipo;
    }

    public function getNombreEquipo(): string
    {
        return $this->nombre_equipo;
    }

    public function getAcronimo(): string
    {
        return $this->acronimo;
    }

    public function getUrlFotoPerfil(): string
    {
        return $this->url_foto_perfil;
    }

    public function getLema(): string
    {
        return $this->lema;
    }

    public function getDescripcionElo(): string
    {
        return $this->descripcion_elo;
    }

    public function getDeportividad(): int
    {
        return $this->deportividad;
    }

    public function getEloActual(): int
    {
        return $this->elo_actual;
    }

    public function getTipoEquipo(): string
    {
        return $this->tipoEquipo;
    }
}
