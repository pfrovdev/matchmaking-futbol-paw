<?php

namespace Paw\App\Services\Impl;

use DateTime;
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

    public function getComentariosByEquipoPaginated(int $idEquipo, int $page, int $perPage, string $orderBy, string $direction): array
    {
        $offset = ($page - 1) * $perPage;

        $comentariosRaw = $this->comentarioDataMapper->findByEquipoPaginated($idEquipo, $perPage, $offset, $orderBy, $direction);

        $comentariosEquipoDtos = [];
        foreach ($comentariosRaw as $comentario) {
            $equipoComentador       = $this->getEquipoComentador($comentario);
            $equipoComentadorBanner = $this->equipoService->getEquipoBanner($equipoComentador);
            $comentarioEquipoDto    = new ComentarioEquipoDto($comentario, $equipoComentadorBanner);
            $comentariosEquipoDtos[]  = $comentarioEquipoDto;
        }

        $totalItems = $this->comentarioDataMapper->countByEquipo($idEquipo);

        $totalPages = (int) ceil($totalItems / $perPage);

        $meta = [
            'totalItems' => $totalItems,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];

        return [
            'data' => $comentariosEquipoDtos,
            'meta' => $meta,
        ];
    }

    function getCantidadDeVotosByIdEquipo(int $idEquipo): int
    {
        return $this->comentarioDataMapper->countByEquipo($idEquipo);
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

    public function comentarEquipoRival(int $idEquipoComentador, int $idEquipoComentado, int $deportividad, string $comentario): void
    {
        if ($idEquipoComentador === $idEquipoComentado) {
            throw new \RuntimeException('No se puede comentar el equipo propio');
        }

        if ($deportividad < 1 || $deportividad > 5) {
            throw new \RuntimeException('La deportividad debe estar entre 1 y 5');
        }

        if (trim($comentario) === '') {
            throw new \RuntimeException('El comentario no puede estar vacío');
        }

        if (! $this->equipoService->getEquipoById($idEquipoComentador)) {
            throw new \RuntimeException('Equipo comentador no encontrado');
        }
        if (! $this->equipoService->getEquipoById($idEquipoComentado)) {
            throw new \RuntimeException('Equipo comentado no encontrado');
        }

        $c = new Comentario();
        $c->setIdEquipoComentador($idEquipoComentador);
        $c->setIdEquipoComentado($idEquipoComentado);
        $c->setDeportividad($deportividad);
        $c->setComentario($comentario);
        $c->setFechaCreacion((new \DateTime())->format('Y-m-d H:i:s'));

        $this->comentarioDataMapper->insertarComentario($c);
    }
}
