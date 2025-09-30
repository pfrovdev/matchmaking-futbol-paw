<?php

namespace Paw\App\Services;

use Paw\App\Dtos\FormularioPartidoDto;
use Paw\App\Enums\ProcesarFormularioEstado;
use Paw\App\Models\Desafio;
use Paw\App\Models\FormularioPartido;

interface PartidoService
{
    function crearPendienteParaDesafio(Desafio $d): int;
    function finalizarPartido(int $partidoId): void;
    function getHistorialPartidosByIdEquipo(int $idEquipo, int $page, int $perPage, string $orderBy, string $direction): array;
    function getResultadoPartidosByIdEquipo(int $idEquipo): array;
    function existePartidoPorTerminar(int $muequipo, int $equipoRival): bool;
    function getProximosPartidos(int $idEquipo, int $page, int $perPage, string $orderBy, string $direction): array;
    function validarPartido(int $idPartido, int $Idequipo): bool;
    function getUltimosFormulariosEquipoContrario(int $id_partido, int $id_equipo): ?FormularioPartidoDto;
    function getUltimaIteracion(int $id_partido, int $id_equipo): int;
    function getEquipoRival(int $idPartido, int $idEquipo): int;
    function procesarFormulario(int $idEquipo, int $idPartido, FormularioPartido $formularioLocal, FormularioPartido $formularioVisitante): ProcesarFormularioEstado;
    function partidoAcordado(int $idEquipo, int $idEquipoRival, int $idPartido): bool;
    function partidoAcordadoYNoFinalizado(int $idEquipo, int $idEquipoRival, int $idPartido): bool;
    function manejarDeadlineSiCorresponde(int $idPartido): bool;

    function terminarPartido(int $partidoId, int $idEquipo, int $idEquipoRival): void;
    function cancelarPartido(int $partidoId, int $idEquipo): bool;

    /**
     * @return Partido|null
     */
    public function getPartidoById(int $idPartido);

}