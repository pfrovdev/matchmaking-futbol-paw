<?php
namespace Paw\App\Services;

use Paw\App\Models\Partido;
use Paw\App\Models\Desafio;

interface PartidoService
{
    public function crearPendienteParaDesafio(Desafio $d): int;
    public function finalizarPartido(int $partidoId): void;
    function getHistorialPartidosByIdEquipo(int $idEquipo): array;
}
