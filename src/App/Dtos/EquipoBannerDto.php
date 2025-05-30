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
    public string $descripcion_elo;

    public function __construct(Equipo $equipo, string $descripcion_elo)
    {
        $this->id_equipo = $equipo->getIdEquipo();
        $this->nombre_equipo = $equipo->getNombre();
        $this->acronimo = $equipo->getAcronimo();
        $this->url_foto_perfil = $equipo->getUrlFotoPerfil();
        $this->lema = $equipo->getLema();
        $this->descripcion_elo = $descripcion_elo;
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
}
