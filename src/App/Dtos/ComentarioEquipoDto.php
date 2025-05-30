<?php

namespace Paw\App\Dtos;

use Paw\App\Models\Comentario;

class ComentarioEquipoDto
{
    private int $idComentario;
    private EquipoBannerDto $equipoComentador;
    private string $comentario;
    private string $fechaCreacion;

    public function __construct(Comentario $comentario, EquipoBannerDto $equipoComentador)
    {
        $this->idComentario = $comentario->getComentarioId();
        $this->comentario = $comentario->getComentario();
        $this->fechaCreacion = $comentario->getFechaCreacion();
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
}
