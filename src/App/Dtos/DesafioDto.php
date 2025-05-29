<?php

namespace Paw\App\Dtos;

use DateTime;
use Paw\App\Models\Desafio;
use Paw\App\Models\Equipo;

class DesafioDto
{
    private int $idDesafio;
    private int $equipoId;
    private string $nombreEquipo;
    private int $deportividad;
    private string $lema;
    private string $urlFotoDePerfil;
    private string $descripcionElo;
    private DateTime $fechaCreacion;

    public function __construct(Equipo $equipoDesafiante, Desafio  $desafio, int $deportividad, string $descripcionElo)
    {
        $this->idDesafio = $desafio->getIdDesafio();
        $this->equipoId = $equipoDesafiante->getIdEquipo();
        $this->nombreEquipo = $equipoDesafiante->getNombre();
        $this->deportividad = $deportividad;
        $this->lema = $equipoDesafiante->getLema();
        $this->urlFotoDePerfil = $equipoDesafiante->getUrlFotoPerfil();
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
    public function getFechaCreacion(): DateTime
    {
        return $this->fechaCreacion;
    }
}
