<?php

namespace Paw\App\Dtos;

use Paw\App\Models\Equipo;

class ResultadoPartidoDto
{
    public EquipoBannerDto $equipo;
    public int $tarjetas_amarillas;
    public int $tarjetas_rojas;
    public int $goles;
    public int $eloConseguido;


    public function __construct(EquipoBannerDto $equipo, int $tarjetas_amarillas, int $tarjetas_rojas, int $goles, int $eloConseguido)
    {
        $this->equipo = $equipo;
        $this->goles = $goles;
        $this->tarjetas_amarillas = $tarjetas_amarillas;
        $this->tarjetas_rojas = $tarjetas_rojas;
        $this->eloConseguido = $eloConseguido;
    }

    public function getEquipo(): EquipoBannerDto
    {
        return $this->equipo;
    }

    public function getTarjetasAmarillas(): int
    {
        return $this->tarjetas_amarillas;
    }

    public function getTarjetasRojas(): int
    {
        return $this->tarjetas_rojas;
    }

    public function getGoles(): int
    {
        return $this->goles;
    }

    public function getEloConseguido(): int
    {
        return $this->eloConseguido;
    }
}