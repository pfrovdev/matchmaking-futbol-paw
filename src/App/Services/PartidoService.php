<?php

namespace Paw\App\Services;

use Paw\App\Dtos\FormularioPartidoDto;
use Paw\App\Models\Desafio;

interface PartidoService
{
    public function crearPendienteParaDesafio(Desafio $d): int;
    public function finalizarPartido(int $partidoId): void;
    function getHistorialPartidosByIdEquipo(int $idEquipo): array;
    function getResultadoPartidosByIdEquipo(int $idEquipo): array;
    function getProximosPartidos(int $idEquipo, int $page, int $perPage, string $orderBy, string $direction): array;
    function validarPartido(int $idPartido, int $Idequipo): bool;
    function getUltimosFormulariosEquipoContrario(int $id_partido, int $id_equipo): ?FormularioPartidoDto;
    function getUltimaIteracion(int $id_partido, int $id_equipo): int;
    function getEquipoRival(int $idPartido, int $idEquipo): int;
}
