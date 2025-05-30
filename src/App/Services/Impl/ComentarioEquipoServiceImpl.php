<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\ComentarioDataMapper;
use Paw\App\DataMapper\EquipoDataMapper;
use Paw\App\Dtos\ComentarioEquipoDto;
use Paw\App\Models\Comentario;
use Paw\App\Models\Equipo;
use Paw\App\Services\ComentarioEquipoService;
use Paw\Core\Database\QueryBuilder;

class ComentarioEquipoServiceImpl implements ComentarioEquipoService
{
    private ComentarioDataMapper $comentarioDataMapper;
    private EquipoDataMapper $equipoDataMapper;

    public function __construct(ComentarioDataMapper $comentarioDataMapper, EquipoDataMapper $equipoDataMapper)
    {
        $this->comentarioDataMapper = $comentarioDataMapper;
        $this->equipoDataMapper = $equipoDataMapper;
    }

    function getComentariosByEquipo(int $idEquipo): array
    {
        $comentarios = $this->comentarioDataMapper->findByEquipo($idEquipo);
        $comentariosEquipoDtos = [];
        foreach ($comentarios as $comentario) {
            $equipoComentador = $this->getEquipoComentador($comentario);
            $comentarioEquipoDto = new ComentarioEquipoDto($comentario, $equipoComentador);
            $comentariosEquipoDtos[] = $comentarioEquipoDto;
        }
        return $comentariosEquipoDtos;
    }

    function getComentarioById(int $idComentario)
    {
        return $this->comentarioDataMapper->findById(['id_comentario' => $idComentario]);
    }

    function saveNewComentario(Comentario $comentario)
    {
        return $this->comentarioDataMapper->saveNewComentario($comentario);
    }

    function getEquipoComentador(Comentario $comentario): ?Equipo
    {
        $id = $comentario->getEquipoComentadorId();
        if (!$id) {
            throw new \RuntimeException("Comentario sin : " . $comentario->getComentario() . " id_equipo_comentador: " . $comentario->getEquipoComentadorId());
        }
        return $this->equipoDataMapper->findById([
            'id_equipo' => $id
        ]);
    }
}
