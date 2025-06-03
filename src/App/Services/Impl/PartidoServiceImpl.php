<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\PartidoDataMapper;
use Paw\App\DataMapper\EstadoPartidoDataMapper;
use Paw\App\Dtos\PartidoDto;
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
use Paw\App\Services\EquipoService;

class PartidoServiceImpl implements PartidoService
{
    private PartidoDataMapper $partidoDataMapper;
    private EstadoPartidoDataMapper $estadoPartidoDataMapper;
    private DesafioDataMapper $desafioDataMapper;
    private EquipoService $equipoService;
    private NivelEloDataMapper $nivelEloDataMapper;
    private ResultadoPartidoDataMapper $resultadoPartidoDataMapper;
    public function __construct(
        PartidoDataMapper $partidoDataMapper,
        EstadoPartidoDataMapper $estadoPartidoDataMapper,
        DesafioDataMapper $desafioDataMapper,
        EquipoService $equipoService,
        NivelEloDataMapper $nivelEloDataMapper,
        ResultadoPartidoDataMapper $resultadoPartidoDataMapper
    ) {
        $this->partidoDataMapper = $partidoDataMapper;
        $this->estadoPartidoDataMapper = $estadoPartidoDataMapper;
        $this->desafioDataMapper = $desafioDataMapper;
        $this->equipoService = $equipoService;
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

    public function getResultadoPartidosByIdEquipo(int $idEquipo): array{
        $resultadoPartidosDisputados = $this->resultadoPartidoDataMapper->findByIdEquipo($idEquipo);
        return $resultadoPartidosDisputados;
    }

    public function getProximosPartidos(int $idEquipo): array{
        $estadoPartidoPendiente = $this->estadoPartidoDataMapper->findIdByCode('pendiente');
        //Obtenemos todos los partidos pendientes
        $partidosPendientes = $this->partidoDataMapper->getAll(['id_estado_partido' => $estadoPartidoPendiente]);
        $misPartidosPendientes = [];
        foreach ($partidosPendientes as $partidoPendiente) {
            // Obtenemos el desafio a partir del partido para obtener el equipo
            $getDesafio = $this->desafioDataMapper->findById(['id_partido' => $partidoPendiente->getIdPartido()]);       
            
            if($getDesafio && ((int)$getDesafio->getIdEquipoDesafiante() === (int)$idEquipo || (int)$getDesafio->getIdEquipoDesafiado() === (int)$idEquipo)){
                
                if ($getDesafio->getIdEquipoDesafiante() !== $idEquipo){
                    $equipo = $this->equipoService->getEquipoBanner($this->equipoService->getEquipoById((int)$getDesafio->getIdEquipoDesafiante()));
                }else{
                    $equipo = $this->equipoService->getEquipoBanner($this->equipoService->getEquipoById((int)$getDesafio->getIdEquipoDesafiado()));
                }
                $misPartidosPendientes[] = new PartidoDto($equipo, $partidoPendiente->getIdPartido(), $partidoPendiente->getFinalizado());
            }
        }
        return $misPartidosPendientes;
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
                    $resultado->getIdEquipoLocal(),
                    $resultado->getTotalAmarillasLocal(),
                    $resultado->getTotalRojasLocal(),
                    $resultado->getGolesEquipoLocal(),
                    $resultado->getEloInicialLocal(),
                    $resultado->getEloFinalLocal()
                );

                $dtoPerdedor = $this->buildResultadoDtoPorEquipo(
                    $resultado->getIdEquipoVisitante(),
                    $resultado->getTotalAmarillasVisitante(),
                    $resultado->getTotalRojasVisitante(),
                    $resultado->getGolesEquipoVisitante(),
                    $resultado->getEloInicialVisitante(),
                    $resultado->getEloFinalVisitante()
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
        $banner = $this->equipoService->getEquipoBanner($this->equipoService->getEquipoById($idEquipo));
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
