<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\PartidoDataMapper;
use Paw\App\DataMapper\EstadoPartidoDataMapper;
use Paw\App\Services\PartidoService;
use Paw\App\Models\Partido;
use Paw\App\Models\Desafio;
use DateTime;
use Paw\App\DataMapper\DesafioDataMapper;
use Paw\App\DataMapper\EquipoDataMapper;
use Paw\App\DataMapper\NivelEloDataMapper;
use Paw\App\DataMapper\ResultadoPartidoDataMapper;
use Paw\App\Dtos\EquipoBannerDto;
use Paw\App\Dtos\HistorialPartidoDto;
use Paw\App\Dtos\ResultadoPartidoDto;

class PartidoServiceImpl implements PartidoService
{
    private PartidoDataMapper $partidoDataMapper;
    private EstadoPartidoDataMapper $estadoPartidoDataMapper;
    private DesafioDataMapper $desafioDataMapper;
    private EquipoDataMapper $equipoDataMapper;
    private NivelEloDataMapper $nivelEloDataMapper;
    private ResultadoPartidoDataMapper $resultadoPartidoDataMapper;
    public function __construct(
        PartidoDataMapper $partidoDataMapper,
        EstadoPartidoDataMapper $estadoPartidoDataMapper,
        DesafioDataMapper $desafioDataMapper,
        EquipoDataMapper $equipoDataMapper,
        NivelEloDataMapper $nivelEloDataMapper,
        ResultadoPartidoDataMapper $resultadoPartidoDataMapper
    ) {
        $this->partidoDataMapper = $partidoDataMapper;
        $this->estadoPartidoDataMapper = $estadoPartidoDataMapper;
        $this->desafioDataMapper = $desafioDataMapper;
        $this->equipoDataMapper = $equipoDataMapper;
        $this->nivelEloDataMapper = $nivelEloDataMapper;
        $this->resultadoPartidoDataMapper = $resultadoPartidoDataMapper;
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

    public function getHistorialPartidosByIdEquipo(int $idEquipo): array
    {
        $desafios = $this->desafioDataMapper->findAllByEquipoAndEstado($idEquipo, 1);
        $historial = [];

        foreach ($desafios as $desafio) {
            $partido   = $this->partidoDataMapper->findByIdAndFinalizado($desafio->getIdPartido(), true);
            $resultado = $this->resultadoPartidoDataMapper->findByIdPartido($desafio->getIdPartido());
            if ($partido && $resultado) {
                // ahora uso un único método para ganador y perdedor
                $dtoGanador  = $this->buildResultadoDtoPorEquipo(
                    $resultado->getIdEquipoGanador(),
                    $resultado->getTotalAmarillasGanador(),
                    $resultado->getTotalRojasGanador(),
                    $resultado->getGolesEquipoGanador(),
                    $resultado->getEloInicialGanador(),
                    $resultado->getEloFinalGanador()
                );

                $dtoPerdedor = $this->buildResultadoDtoPorEquipo(
                    $resultado->getIdEquipoPerdedor(),
                    $resultado->getTotalAmarillasPerdedor(),
                    $resultado->getTotalRojasPerdedor(),
                    $resultado->getGolesEquipoPerdedor(),
                    $resultado->getEloInicialPerdedor(),
                    $resultado->getEloFinalPerdedor()
                );

                $soyDesafiante = ($idEquipo === $desafio->getIdEquipoDesafiante());

                $historial[] = new HistorialPartidoDto(
                    $partido->getFechaFinalizacion(),
                    $dtoPerdedor,
                    $dtoGanador,
                    $soyDesafiante,
                    false
                );
            }
        }

        return $historial;
    }

    private function buildResultadoDtoPorEquipo(
        int $idEquipo,
        int    $amarillas,
        int    $rojas,
        int    $goles,
        int    $eloInicial,
        int    $eloFinal
    ): ResultadoPartidoDto {
        $equipo      = $this->equipoDataMapper->findById(['id_equipo' => $idEquipo]);
        $descElo     = $this->nivelEloDataMapper->findDescripcionById($equipo->getIdNivelElo());
        $banner      = new EquipoBannerDto($equipo, $descElo);
        return new ResultadoPartidoDto(
            $banner,
            $amarillas,
            $rojas,
            $goles,
            $eloInicial,
            $eloFinal - $eloInicial
        );
    }
}
