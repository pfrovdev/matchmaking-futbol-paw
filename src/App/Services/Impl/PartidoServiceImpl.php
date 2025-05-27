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
    private PartidoDataMapper $pdm;
    private EstadoPartidoDataMapper $epdm;

    public function __construct(QueryBuilder $qb)
    {
        $this->pdm  = new PartidoDataMapper($qb);
        $this->epdm = new EstadoPartidoDataMapper($qb);
    }

    public function crearPendienteParaDesafio(Desafio $d): int
    {
        $fechaPendiente = (new DateTime())->format('Y-m-d H:i:s');
        $idPendiente = $this->epdm->findIdByCode('pendiente');

        $p = new Partido();
        $p->iniciarPendiente($fechaPendiente, $idPendiente);

        $newId = $this->pdm->insertPartido($p);
        $p->set(['id_partido' => $newId]);

        return $newId;
    }

    public function finalizarPartido(int $partidoId): void
    {
        $p = $this->pdm->findById($partidoId);
        if (! $p) {
            throw new \InvalidArgumentException("Partido $partidoId no existe");
        }

        $fechaFinal = (new DateTime())->format('Y-m-d H:i:s');
        $idFinal = $this->epdm->findIdByCode('finalizado');

        $p->finalizar($fechaFinal, $idFinal);
        $this->pdm->updatePartido($p);
    }
}
