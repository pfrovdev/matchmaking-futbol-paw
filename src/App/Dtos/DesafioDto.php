<?php

namespace Paw\App\Dtos;

use Paw\App\Models\Desafio;
use Paw\App\Models\Equipo;

class DesafioDto
{
    private int $idDesafio;
    private int $equipoId;
    private string $nombreEquipo;
    private int $deportividad;
    private string $lema;
    private string $acronimo;
    private string $urlFotoDePerfil;
    private string $descripcionElo;
    private string $fechaCreacion;
    private string $idNivelElo;

    public function __construct(Equipo $equipoDesafiante, Desafio  $desafio, int $deportividad, string $descripcionElo)
    {
        $this->idDesafio = $desafio->getIdDesafio();
        $this->acronimo = $equipoDesafiante->getAcronimo();
        $this->equipoId = $equipoDesafiante->getIdEquipo();
        $this->nombreEquipo = $equipoDesafiante->getNombre();
        $this->deportividad = $deportividad;
        $this->lema = $equipoDesafiante->getLema();
        $this->descripcionElo = $descripcionElo;
        $this->urlFotoDePerfil = $equipoDesafiante->getUrlFotoPerfil();
        $this->fechaCreacion = $desafio->getFechaCreacion();
        $this->idNivelElo = $equipoDesafiante->getIdNivelElo();
    }

    public function getIdDesafio(): int
    {
        return $this->idDesafio;
    }
    public function getEquipoId(): int
    {
        return $this->equipoId;
    }
    public function getNombreEquipo(): string
    {
        return $this->nombreEquipo;
    }
    public function getDeportividad(): int
    {
        return $this->deportividad;
    }
    public function getLema(): string
    {
        return $this->lema;
    }
    public function getUrlFotoDePerfil(): string
    {
        return $this->urlFotoDePerfil;
    }
    public function getDescripcionElo(): string
    {
        return $this->descripcionElo;
    }
    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function getAcronimo(): string
    {
        return $this->acronimo;
    }

    public function getIdNivelElo(): int
    {
        return $this->idNivelElo;
    }
}
