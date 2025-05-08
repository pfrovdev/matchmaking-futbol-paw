<?php

namespace Paw\App\Utils;

use Paw\App\Models\ResultadoPartido;
use Paw\App\Models\Equipo;

class CalculadoraDeElo {

    public static function calcularCambioElo(ResultadoPartido $partido, Equipo $equipo): int {
        if ($partido->soyEquipoGanador($equipo)) {
            return $partido->fields['elo_final_ganador'] - $partido->fields['elo_inicial_ganador'];
        }
        return $partido->fields['elo_final_perdedor'] - $partido->fields['elo_inicial_perdedor'];
    }
}
?>