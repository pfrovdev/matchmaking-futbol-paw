<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\ComentarioDataMapper;
use Paw\App\Dtos\ComentarioEquipoDto;
use Paw\App\Models\Comentario;
use Paw\App\Models\Equipo;
use Paw\App\Services\ComentarioEquipoService;
use Paw\App\Services\EquipoService;

class ComentarioEquipoServiceImpl implements ComentarioEquipoService
{
    private ComentarioDataMapper $comentarioDataMapper;
    private EquipoService $equipoService;

    public function __construct(ComentarioDataMapper $comentarioDataMapper, EquipoService $equipoService)
    {
        $this->comentarioDataMapper = $comentarioDataMapper;
        $this->equipoService = $equipoService;
    }

    function getComentariosByEquipo(int $idEquipo): array
    {
        $comentarios = $this->comentarioDataMapper->findByEquipo($idEquipo);
        $comentariosEquipoDtos = [];
        foreach ($comentarios as $comentario) {
            $equipoComentador = $this->getEquipoComentador($comentario);
            $equipoComentadorBanner = $this->equipoService->getEquipoBanner($equipoComentador);
            $comentarioEquipoDto = new ComentarioEquipoDto($comentario, $equipoComentadorBanner);
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
        return $this->equipoService->getEquipoById($id);
    }
}
