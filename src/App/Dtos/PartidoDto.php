<?php

namespace Paw\App\Dtos;

use Paw\App\Models\Equipo;

class PartidoDto
{
    public EquipoBannerDto $equipo;
    public int $id_partido;
    public bool $finalizado;

    public function __construct(EquipoBannerDto $equipo, int $id_partido, bool $finalizado)
    {
        $this->id_partido  = $id_partido;
        $this->equipo = $equipo;
        $this->finalizado = $finalizado;
    }

    public function getEquipo(): EquipoBannerDto
    {
        return $this->equipo;
    }

    public function getIdPartido(): int
    {
        return $this->id_partido;
    }

    public function getFinalizado(): bool
    {
        return $this->finalizado;
    }
   
}
