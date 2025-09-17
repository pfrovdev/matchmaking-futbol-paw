<?php
namespace Paw\App\Services;

use Paw\App\Models\Estadisticas;

interface EstadisticaService
{
    function findEstadisticasByIdEquipo(int $id_equipo): Array;
}