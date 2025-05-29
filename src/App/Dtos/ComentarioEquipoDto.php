<?php

namespace Paw\App\Dtos;

use Paw\App\Models\Comentario;
use Paw\App\Models\Equipo;

class ComentarioEquipoDto
{
    private int $idComentario;
    private int $idEquipoComentador;
    private string $nombreEquipoComentador;
    private string $comentario;
    private int $deportividad;
    private string $fechaCreacion;

    public function __construct(Comentario $comentario, Equipo $equipoComentador)
    {
        $this->idComentario = $comentario->getComentarioId();
        $this->idEquipoComentador = $equipoComentador->getIdEquipo();
        $this->nombreEquipoComentador = $equipoComentador->getNombre();
        $this->comentario = $comentario->getComentario();
        $this->deportividad = $comentario->getDeportividad();
        $this->fechaCreacion = $comentario->getFechaCreacion();
    }

    public function getIdComentario(): int
    {
        return $this->idComentario;
    }
    public function getIdEquipoComentador(): int
    {
        return $this->idEquipoComentador;
    }
    public function getNombreEquipoComentador(): string
    {
        return $this->nombreEquipoComentador;
    }
    public function getComentario(): string
    {
        return $this->comentario;
    }
    public function getDeportividad(): int
    {
        return $this->deportividad;
    }
    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }
}
