<?php

namespace Paw\App\Dtos;

class HistorialPartidoDto
{
    public string $fecha_finalizacion;
    public ResultadoPartidoDto $resultadoGanador;
    public ResultadoPartidoDto $resultadoPerdedor;
    public bool $esEmpate = false;

    public function __construct(string $fecha_finalizacion, ResultadoPartidoDto $resultadoPerdedor, ResultadoPartidoDto $resultadoGanador, bool $esEmpate = false)
    {
        $this->fecha_finalizacion = $fecha_finalizacion;
        $this->resultadoPerdedor = $resultadoPerdedor;
        $this->resultadoGanador = $resultadoGanador;
        $this->esEmpate = $esEmpate;
    }


    public function getFechaFinalizacion(): string
    {
        return $this->fecha_finalizacion;
    }

    public function getResultadoGanador(): ResultadoPartidoDto
    {
        return $this->resultadoGanador;
    }

    public function getResultadoPerdedor(): ResultadoPartidoDto
    {
        return $this->resultadoPerdedor;
    }

}