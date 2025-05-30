<?php

namespace Paw\App\Services;

use Paw\App\Models\Comentario;
use Paw\App\Models\Equipo;

interface ComentarioEquipoService
{
    function getComentariosByEquipo(int $idEquipo): array;
    function getComentarioById(int $idComentario);
    function saveNewComentario(Comentario $comentario);
    function getEquipoComentador(Comentario $comentario): ?Equipo;
}
