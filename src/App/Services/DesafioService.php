<?php

namespace Paw\App\Services;

interface DesafioService {
    function acceptDesafio(int $desafioId);
    function createDesafio(int $eqA, int $eqB);
    function rejectDesafio(int $desafioId);
}