<?php

namespace Paw\App\Dtos;

class FormularioPartidoDto
{
    public int $id_partido;
    public int $id_equipo;
    public int $iteracionActual;
    public FormularioEquipoDto $equipo_local;
    public FormularioEquipoDto $equipo_visitante;

    public function __construct(int $id_equipo, int $id_partido, int $iteracionActual, FormularioEquipoDto $equipo_local, FormularioEquipoDto $equipo_visitante)
    {
        $this->id_equipo = $id_equipo;
        $this->id_partido = $id_partido;
        $this->iteracionActual = $iteracionActual;
        $this->equipo_local = $equipo_local;
        $this->equipo_visitante = $equipo_visitante;
    }

    public function getIdPartido(): int
    {
        return $this->id_partido;
    }

    public function getIteracionActual(): int
    {
        return $this->iteracionActual;
    }

    public function getEquipoLocal(): FormularioEquipoDto
    {
        return $this->equipo_local;
    }

    public function getEquipoVisitante(): FormularioEquipoDto
    {
        return $this->equipo_visitante;
    }

    public function getIdEquipo() : int
    {
        return $this->id_equipo;
    }

}