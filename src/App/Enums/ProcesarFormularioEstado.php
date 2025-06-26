<?php

namespace Paw\App\Enums;

enum ProcesarFormularioEstado: string
{
    case MAXIMAS_ITERACIONES_ALCANZADAS = 'maximas_iteraciones_alcanzadas';
    case FUERA_DE_TURNO = 'fuera_de_turno';
    case PARTIDO_TERMINADO = 'partido_terminado';
    case NUEVA_ITERACION = 'nueva_iteracion';
    case PARTIDO_NO_ACORDADO = 'partido_no_acordado';
}

?>
