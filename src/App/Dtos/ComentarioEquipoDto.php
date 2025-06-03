<?php

namespace Paw\App\Dtos;

use JsonSerializable;
use Paw\App\Models\Comentario;

class ComentarioEquipoDto implements JsonSerializable
{
    private int $idComentario;
    private EquipoBannerDto $equipoComentador;
    private string $comentario;
    private string $fechaCreacion;
    private int $deportividad;

    public function __construct(Comentario $comentario, EquipoBannerDto $equipoComentador)
    {
        $this->idComentario = $comentario->getComentarioId();
        $this->comentario = $comentario->getComentario();
        $this->fechaCreacion = $comentario->getFechaCreacion();
        $this->deportividad = $comentario->getDeportividad();
        $this->equipoComentador = $equipoComentador;
    }

    public function getIdComentario(): int
    {
        return $this->idComentario;
    }
    public function getComentario(): string
    {
        return $this->comentario;
    }

    public function getEquipoComentador(): EquipoBannerDto
    {
        return $this->equipoComentador;
    }

    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function getDeportividad(): int
    {
        return $this->deportividad;
    }

    public function jsonSerialize(): array
    {
        return [
            'idComentario'     => $this->idComentario,
            'deportividad'     => $this->deportividad,
            'comentario'       => $this->comentario,
            'fechaCreacion'    => $this->fechaCreacion,
            'equipoComentador' => $this->equipoComentador, 
        ];
    }
}
