<?php

namespace Paw\App\Services;

use Paw\App\Models\Equipo;

interface EquipoService {
    function getAllTiposEquipos();
    function getTypeTeamById(int $idTypeTeam);

    function saveNewTeam(Equipo $equipo);
}