<?php

namespace Paw\App\Dtos;

use JsonSerializable;
use Paw\App\Models\Equipo;

class PartidoDto implements JsonSerializable
{
    public EquipoBannerDto $equipo;
    public int $id_partido;
    public bool $finalizado;
    private ?string $fechaCreacion;

    public function __construct(EquipoBannerDto $equipo, int $id_partido, bool $finalizado, string $fechaCreacion)
    {
        $this->id_partido  = $id_partido;
        $this->equipo = $equipo;
        $this->finalizado = $finalizado;
        $this->fechaCreacion = $fechaCreacion;
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

    public function getFechaCreacion(): ?string
    {
        return $this->fechaCreacion;
    }

    public function jsonSerialize(): array
    {
        return [
            'id_partido' => $this->id_partido,
            'equipo' => $this->equipo,
            'finalizado' => $this->finalizado,
            'fecha_creacion' => $this->fechaCreacion
        ];
    }
   
}
