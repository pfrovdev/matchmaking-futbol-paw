<?php

namespace Paw\App\Services;

use Paw\App\Models\Comentario;
use Paw\App\Models\Equipo;

interface ComentarioEquipoService
{
    function getComentariosByEquipoPaginated(int $idEquipo, int $page, int $perPage, string $orderBy, string $direction): array;
    function getComentarioById(int $idComentario);
    function saveNewComentario(Comentario $comentario);
    function getEquipoComentador(Comentario $comentario): ?Equipo;
}
