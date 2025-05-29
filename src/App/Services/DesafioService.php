<?php

namespace Paw\App\Services;

use Paw\App\Dtos\DesafioDto;
use Paw\App\Models\Desafio;

interface DesafioService {
    function acceptDesafio(int $desafioId): Desafio;
    function createDesafio(int $eqA, int $eqB): Desafio;
    function rejectDesafio(int $desafioId): Desafio;
    function getDesafiosByEquipoAndEstadoDesafio(int $idEquipo, string $estado): array;
}