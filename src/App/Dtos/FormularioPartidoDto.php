<?php

namespace Paw\App\Dtos;

class FormularioPartidoDto
{
    public int $id_partido;
    public FormularioEquipoDto $equipo_local;
    public FormularioEquipoDto $equipo_visitante;
}