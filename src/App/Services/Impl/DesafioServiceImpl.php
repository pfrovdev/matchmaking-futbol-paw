<?php
namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\DesafioDataMapper;
use Paw\App\DataMapper\EstadoDesafioDataMapper;
use Paw\App\Services\DesafioService;
use Paw\App\Services\PartidoService;
use Paw\App\Models\Desafio;
use Paw\Core\Database\QueryBuilder;
use DateTime;

class DesafioServiceImpl implements DesafioService
{
    private DesafioDataMapper $dm;
    private EstadoDesafioDataMapper $edm;
    private PartidoService $partidoSrv;

    public function __construct(QueryBuilder $qb, PartidoService $partidoSrv) {
        $this->dm = new DesafioDataMapper($qb);
        $this->edm = new EstadoDesafioDataMapper($qb);
        $this->partidoSrv= $partidoSrv;
    }

    public function createDesafio(int $eqA, int $eqB): Desafio
    {
        $fecha = (new DateTime())->format('Y-m-d H:i:s');
        $idPendiente = $this->edm->findIdByCode('pendiente');

        $d = new Desafio();
        $d->set([
            'id_equipo_desafiante' => $eqA,
            'id_equipo_desafiado'  => $eqB,
            'fecha_creacion'       => $fecha,
            'id_estado_desafio'    => $idPendiente,
        ]);

        $newId = $this->dm->insertDesafio($d);
        $d->set(['id_desafio' => $newId]);

        return $d;
    }

    public function acceptDesafio(int $desafioId): void
    {
        $d = $this->dm->findById(['id_desafio' => $desafioId]);
        if (! $d) {
            throw new \InvalidArgumentException("Desafío $desafioId no existe");
        }

        $fecha = (new DateTime())->format('Y-m-d H:i:s');
        $idAcepto = $this->edm->findIdByCode('aceptado');
        $d->aceptar($fecha, $idAcepto);

        $this->dm->updateDesafio($d);

        $partidoId = $this->partidoSrv->crearPendienteParaDesafio($d);
        $d->asignarPartido($partidoId);

        $this->dm->updateDesafio($d);
    }

    public function rejectDesafio(int $desafioId): void
    {
        $d = $this->dm->findById(['id_desafio' => $desafioId]);
        if (! $d) {
            throw new \InvalidArgumentException("Desafío $desafioId no existe");
        }
        $idRechazo = $this->edm->findIdByCode('rechazado');
        $d->rechazar($idRechazo);

        $this->dm->updateDesafio($d);
    }
}