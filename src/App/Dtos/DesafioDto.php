<?php

namespace Paw\App\Dtos;

use Paw\App\Models\Desafio;
use Paw\App\Models\Equipo;

class DesafioDto
{
    private int $idDesafio;
    private ?string $fechaCreacion;
    private EquipoBannerDto $equipoDesafiante;

    public function __construct(EquipoBannerDto $equipoDesafiante, Desafio  $desafio)
    {
        $this->idDesafio = $desafio->getIdDesafio();
        $this->fechaCreacion = $desafio->getFechaCreacion();
        $this->equipoDesafiante = $equipoDesafiante;
    }

    public function getIdDesafio(): int
    {
        return $this->idDesafio;
    }
    
    public function getFechaCreacion(): ?string
    {
        return $this->fechaCreacion;
    }

    public function getEquipoDesafiante(): EquipoBannerDto
    {
        return $this->equipoDesafiante;
    }
}
