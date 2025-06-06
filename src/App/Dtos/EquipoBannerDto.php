<?php

namespace Paw\App\Dtos;

use JsonSerializable;
use Paw\App\Models\Equipo;

class EquipoBannerDto implements JsonSerializable
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
    public string $numero_telefono;
    public array $resultadosEquipo; 

    public function __construct(Equipo $equipo, string $descripcion_elo, int $deportividad, string $tipoEquipo, ? array $resultadosEquipo)
    {
        $this->id_equipo = $equipo->getIdEquipo();
        $this->nombre_equipo = $equipo->getNombre();
        $this->acronimo = $equipo->getAcronimo();
        $this->url_foto_perfil = $equipo->getUrlFotoPerfil() ?? '';
        $this->lema = $equipo->getLema();
        $this->elo_actual = $equipo->getEloActual();
        $this->descripcion_elo = $descripcion_elo;
        $this->deportividad = $deportividad;
        $this->tipoEquipo = $tipoEquipo;
        $this->numero_telefono = $equipo->getTelefono();
        $this->resultadosEquipo = $resultadosEquipo ?? []; 
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

    public function getNumeroTelefono(): string
    {
        return $this->numero_telefono;
    }

    public function getResultadosEquipo(): array{
        return $this->resultadosEquipo;
    } 

    public function jsonSerialize(): array
    {
        return [
            'id_equipo' => $this->id_equipo,
            'nombre_equipo' => $this->nombre_equipo,
            'acronimo' => $this->acronimo,
            'url_foto_perfil' => $this->url_foto_perfil,
            'lema' => $this->lema,
            'elo_actual' => $this->elo_actual,
            'descripcion_elo' => $this->descripcion_elo,
            'deportividad' => $this->deportividad,
            'tipoEquipo'=> $this->tipoEquipo,
            'numero_telefono' => $this->numero_telefono,
            'resultadosEquipo' => $this->resultadosEquipo
        ];
    }
}
