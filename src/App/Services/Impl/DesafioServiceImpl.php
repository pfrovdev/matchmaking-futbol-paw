<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\DesafioDataMapper;
use Paw\App\DataMapper\EstadoDesafioDataMapper;
use Paw\App\Services\DesafioService;
use Paw\App\Services\PartidoService;
use Paw\App\Models\Desafio;
use Paw\Core\Database\QueryBuilder;
use DateTime;
use Paw\App\DataMapper\EquipoDataMapper;
use Paw\App\Dtos\DesafioDto;
use Paw\App\Models\Equipo;
use Paw\App\Services\EquipoService;
use Paw\Core\Container;

class DesafioServiceImpl implements DesafioService
{
    private DesafioDataMapper $desafioDataMapper;
    private EstadoDesafioDataMapper $estadoDesafioDataMapper;
    private PartidoService $partidoService;
    private EquipoService $equipoService;

    public function __construct(DesafioDataMapper $desafioDataMapper, EstadoDesafioDataMapper $estadoDesafioDataMapper, PartidoService $partidoService, EquipoService $equipoService)
    {
        $this->desafioDataMapper = $desafioDataMapper;
        $this->estadoDesafioDataMapper = $estadoDesafioDataMapper;
        $this->partidoService = $partidoService;
        $this->equipoService = $equipoService;
    }

    public function createDesafio(int $eqA, int $eqB): Desafio
    {
        $fecha = (new DateTime())->format('Y-m-d H:i:s');
        $idPendiente = $this->estadoDesafioDataMapper->findIdByCode('pendiente');

        $d = new Desafio();
        $d->set([
            'id_equipo_desafiante' => $eqA,
            'id_equipo_desafiado' => $eqB,
            'fecha_creacion' => $fecha,
            'id_estado_desafio' => $idPendiente,
        ]);

        $newId = $this->desafioDataMapper->insertDesafio($d);
        $d->set(['id_desafio' => $newId]);

        return $d;
    }

    public function existeDesafioPendiente(int $miEquipo, int $equipoDesafiado): bool
    {
        $estadoId = $this->estadoDesafioDataMapper->findIdByCode('pendiente');

        $desafiosComoDesafiado = $this->desafioDataMapper->yaExisteDesafioPendiente($miEquipo, $estadoId, $equipoDesafiado);

        if (count($desafiosComoDesafiado) > 0) {
            return true;
        }

        $desafiosComoDesafiante = $this->desafioDataMapper->yaExisteDesafioPendiente($equipoDesafiado, $estadoId, $miEquipo);

        if (count($desafiosComoDesafiante) > 0) {
            return true;
        }

        return false;
    }
    public function acceptDesafio(int $desafioId): Desafio
    {
        $d = $this->desafioDataMapper->findById(['id_desafio' => $desafioId]);
        if (!$d) {
            throw new \InvalidArgumentException("Desafío $desafioId no existe");
        }

        $fecha = (new DateTime())->format('Y-m-d H:i:s');
        $idAcepto = $this->estadoDesafioDataMapper->findIdByCode('aceptado');
        $d->aceptar($fecha, $idAcepto);

        $this->desafioDataMapper->updateDesafio($d);

        $partidoId = $this->partidoService->crearPendienteParaDesafio($d);
        $d->asignarPartido($partidoId);

        $this->desafioDataMapper->updateDesafio($d);

        return $d;
    }

    public function rejectDesafio(int $desafioId): Desafio
    {
        $d = $this->desafioDataMapper->findById(['id_desafio' => $desafioId]);
        if (!$d) {
            throw new \InvalidArgumentException("Desafío $desafioId no existe");
        }
        $idRechazo = $this->estadoDesafioDataMapper->findIdByCode('rechazado');
        $d->rechazar($idRechazo);

        $this->desafioDataMapper->updateDesafio($d);
        return $d;
    }

    // desafios donde el equipo es el equipo desafiado (para los desafios pendientes)
    public function getDesafiosByEquipoAndEstadoDesafio(int $equipoId, string $estado, int $page, int $perPage, string $orderBy, string $direction): array
    {
        $estadoId = $this->estadoDesafioDataMapper->findIdByCode($estado);
        if (!$estadoId) {
            throw new \RuntimeException("El estado $estado no existe");
        }

        $equipo = $this->equipoService->getEquipoById($equipoId);

        if (!$equipo) {
            throw new \RuntimeException("Equipo $equipoId no encontrado");
        }

        $offset = ($page - 1) * $perPage;

        $desafios = $this->desafioDataMapper->findByEquipoAndEstadoPaginated($equipoId, $estadoId, $perPage, $offset, $orderBy, $direction);

        $desafiosDtos = [];

        foreach ($desafios as $desafio) {
            $equipoBanner = $this->equipoService->getEquipoBanner($this->equipoService->getEquipoById($desafio->getIdEquipoDesafiante()));
            $desafioDto = new DesafioDto($equipoBanner, $desafio);
            $desafiosDtos[] = $desafioDto;
        }

        $totalItems = $this->desafioDataMapper->countByEquipoAndEstado($equipoId, $estadoId);

        $totalPages = (int) ceil($totalItems / $perPage);

        $meta = [
            'totalItems' => $totalItems,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];

        return [
            'data' => $desafiosDtos,
            'meta' => $meta,
        ];
    }

}
