<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\PartidoDataMapper;
use Paw\App\DataMapper\EstadoPartidoDataMapper;
use Paw\App\Services\PartidoService;
use Paw\App\Models\Partido;
use Paw\App\Models\Desafio;
use Paw\Core\Database\QueryBuilder;
use DateTime;

class PartidoServiceImpl implements PartidoService
{
    private PartidoDataMapper $partidoDataMapper;
    private EstadoPartidoDataMapper $estadoPartidoDataMapper;

    public function __construct(PartidoDataMapper $partidoDataMapper, EstadoPartidoDataMapper $estadoPartidoDataMapper)
    {
        $this->partidoDataMapper  = $partidoDataMapper;
        $this->estadoPartidoDataMapper = $estadoPartidoDataMapper;
    }

    public function crearPendienteParaDesafio(Desafio $d): int
    {
        $fechaPendiente = (new DateTime())->format('Y-m-d H:i:s');
        $idPendiente = $this->estadoPartidoDataMapper->findIdByCode('pendiente');

        $p = new Partido();
        $p->iniciarPendiente($fechaPendiente, $idPendiente);

        $newId = $this->partidoDataMapper->insertPartido($p);
        $p->set(['id_partido' => $newId]);

        return $newId;
    }

    public function finalizarPartido(int $partidoId): void
    {
        $p = $this->partidoDataMapper->findById(['id_partido' => $partidoId]);
        if (! $p) {
            throw new \InvalidArgumentException("Partido $partidoId no existe");
        }

        $fechaFinal = (new DateTime())->format('Y-m-d H:i:s');
        $idFinal = $this->estadoPartidoDataMapper->findIdByCode('finalizado');

        $p->finalizar($fechaFinal, $idFinal);
        $this->partidoDataMapper->updatePartido($p);
    }

    public function getHistorialPartidosByIdEquipo($idEquipo): array
    {
        $partidos = $this->partidoDataMapper->findAllByEquipoAndFinalizado($idEquipo, 1);
        return $partidos;
    }
}
