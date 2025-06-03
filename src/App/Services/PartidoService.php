<?php
namespace Paw\App\Services;

use Paw\App\Models\Partido;
use Paw\App\Models\Desafio;

interface PartidoService
{
    public function crearPendienteParaDesafio(Desafio $d): int;
    public function finalizarPartido(int $partidoId): void;
    function getHistorialPartidosByIdEquipo(int $idEquipo): array;
    function getResultadoPartidosByIdEquipo(int $idEquipo): array;
    function getProximosPartidos(int $idEquipo, int $page, int $perPage, string $orderBy, string $direction): array;
    
}
