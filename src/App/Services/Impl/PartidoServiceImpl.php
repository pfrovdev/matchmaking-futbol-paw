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
use Paw\App\DataMapper\FormularioPartidoDataMapper;
use Paw\App\DataMapper\NivelEloDataMapper;
use Paw\App\DataMapper\ResultadoPartidoDataMapper;
use Paw\App\Dtos\FormularioEquipoDto;
use Paw\App\Dtos\FormularioPartidoDto;
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
    private FormularioPartidoDataMapper $formularioPartidoDataMapper;
    public function __construct(
        PartidoDataMapper $partidoDataMapper,
        EstadoPartidoDataMapper $estadoPartidoDataMapper,
        DesafioDataMapper $desafioDataMapper,
        EquipoService $equipoService,
        NivelEloDataMapper $nivelEloDataMapper,
        ResultadoPartidoDataMapper $resultadoPartidoDataMapper,
        FormularioPartidoDataMapper $formularioPartidoDataMapper
    ) {
        $this->partidoDataMapper = $partidoDataMapper;
        $this->estadoPartidoDataMapper = $estadoPartidoDataMapper;
        $this->desafioDataMapper = $desafioDataMapper;
        $this->equipoService = $equipoService;
        $this->nivelEloDataMapper = $nivelEloDataMapper;
        $this->resultadoPartidoDataMapper = $resultadoPartidoDataMapper;
        $this->formularioPartidoDataMapper = $formularioPartidoDataMapper;
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

    public function getResultadoPartidosByIdEquipo(int $idEquipo): array
    {
        $resultadoPartidosDisputados = $this->resultadoPartidoDataMapper->findByIdEquipo($idEquipo);
        return $resultadoPartidosDisputados;
    }

    public function getProximosPartidos(int $idEquipo, int $page, int $perPage, string $orderBy, string $direction): array
    {
        $estadoPartidoPendiente = $this->estadoPartidoDataMapper->findIdByCode('pendiente');
        //Obtenemos todos los partidos pendientes
        $partidosPendientes = $this->partidoDataMapper->getAll(['id_estado_partido' => $estadoPartidoPendiente]);
        $misPartidosPendientes = [];
        foreach ($partidosPendientes as $partidoPendiente) {
            // Obtenemos el desafio a partir del partido para obtener el equipo
            $getDesafio = $this->desafioDataMapper->findById(['id_partido' => $partidoPendiente->getIdPartido()]);

            if ($getDesafio && ((int)$getDesafio->getIdEquipoDesafiante() === (int)$idEquipo || (int)$getDesafio->getIdEquipoDesafiado() === (int)$idEquipo)) {

                if ($getDesafio->getIdEquipoDesafiante() !== $idEquipo) {
                    $equipo = $this->equipoService->getEquipoBanner($this->equipoService->getEquipoById((int)$getDesafio->getIdEquipoDesafiante()));
                } else {
                    $equipo = $this->equipoService->getEquipoBanner($this->equipoService->getEquipoById((int)$getDesafio->getIdEquipoDesafiado()));
                }
                $misPartidosPendientes[] = new PartidoDto($equipo, $partidoPendiente->getIdPartido(), $partidoPendiente->getFinalizado(), $partidoPendiente->getFechaCreacion());
            }
        }

        $totalPages = (int) ceil(count($misPartidosPendientes) / $perPage);

        $meta = [
            'totalItems' => count($misPartidosPendientes),
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];

        return [
            'data' => $this->proximosPartidosPaginated($misPartidosPendientes, $page, $perPage, $orderBy, $direction),
            'meta' => $meta,
        ];
    }

    private function proximosPartidosPaginated(array $misPartidosPendientes, int $page, int $perPage, string $orderBy, string $direction)
    {
        $orderBy = strtolower($orderBy);
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        if ($orderBy === 'fecha_creacion') {
            usort($misPartidosPendientes, function (PartidoDto $a, PartidoDto $b) use ($direction) {
                $fa = $a->getFechaCreacion() ? strtotime($a->getFechaCreacion()) : 0;
                $fb = $b->getFechaCreacion() ? strtotime($b->getFechaCreacion()) : 0;

                if ($fa === $fb) {
                    return 0;
                }

                if ($direction === 'asc') {
                    return $fa <=> $fb;
                } else {
                    return $fb <=> $fa;
                }
            });
        }

        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;
        $sliced = array_slice($misPartidosPendientes, $offset, $perPage);

        return $sliced;
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

    public function validarPartido(int $idPartido, int $Idequipo): bool
    {
        $partido = $this->partidoDataMapper->findByIdAndFinalizado($idPartido, false);

        if (!$partido) {
            throw new \RuntimeException("Partido con id {$idPartido} no encontrado");
        }

        $desafio = $this->desafioDataMapper->findByIdPartido($idPartido);

        if ($desafio->getIdEquipoDesafiado() !== $Idequipo &&  $desafio->getIdEquipoDesafiante() !== $Idequipo) {
            throw new \RuntimeException("El equipo {$Idequipo} no participó en el partido");
        }

        $formularioPartido = $this->formularioPartidoDataMapper->findByIdFormularioPartido($idPartido);

        if ($formularioPartido->getTotalIteraciones() == 5) {
            throw new \RuntimeException("El formulario llegó a su máximo de iteraciones");
        }

        return true;
    }

    public function getUltimosFormulariosEquipoContrario(int $id_partido, int $id_equipo): ?FormularioPartidoDto
    {
        $formularios = $this->formularioPartidoDataMapper->findByIdPartidoOrderByFechaDesc($id_partido);

        // Filtrar formularios del equipo contrario
        $formulariosContrario = array_filter($formularios, function ($formulario) use ($id_equipo) {
            return $formulario->getIdEquipo() !== $id_equipo;
        });

        if (empty($formulariosContrario)) {
            return null;
        }

        // se agrupa por iteración
        $porIteracion = [];
        foreach ($formulariosContrario as $formulario) {
            $iteracion = $formulario->getTotalIteraciones();
            $porIteracion[$iteracion][] = $formulario;
        }

        // se obtiene la iteración más alta
        $ultimaIteracion = max(array_keys($porIteracion));
        $formulariosUltima = $porIteracion[$ultimaIteracion];

        // se mapea a equipo_local y equipo_visitante
        $resultado = [
            'mi_equipo' => null,
            'equipo_contrario' => null
        ];
        foreach ($formulariosUltima as $formulario) {
            $tipo = $formulario->getTipoFormulario();
            if ($tipo === 'FORMULARIO_MI_EQUIPO') {
                $resultado['mi_equipo'] = $formulario;
            } elseif ($tipo === 'FORMULARIO_EQUIPO_CONTRARIO') {
                $resultado['equipo_contrario'] = $formulario;
            }
        }

        // se crea el DTO
        return new FormularioPartidoDto(
            $resultado['mi_equipo']->getIdEquipo(),
            $id_partido,
            $ultimaIteracion,
            new FormularioEquipoDto(
                $resultado['mi_equipo']->getTotalGoles() ?? 0,
                $resultado['mi_equipo']->getTotalAsistencias() ?? 0,
                $resultado['mi_equipo']->getTotalAmarillas() ?? 0,
                $resultado['mi_equipo']->getTotalRojas() ?? 0
            ),
            new FormularioEquipoDto(
                $resultado['equipo_contrario']->getTotalGoles() ?? 0,
                $resultado['equipo_contrario']->getTotalAsistencias() ?? 0,
                $resultado['equipo_contrario']->getTotalAmarillas() ?? 0,
                $resultado['equipo_contrario']->getTotalRojas() ?? 0
            )
        );
    }

    public function getUltimaIteracion(int $idPartido, int $idEquipo): int
    {
        $formularios = $this->formularioPartidoDataMapper->findByIdPartidoOrderByFechaDesc($idPartido);

        if (!$formularios) {
            return 0;
        }

        return $formularios[0]->getTotalIteraciones() ?? 0;
    }

    public function getEquipoRival(int $idPartido, int $idEquipo): int
    {
        $partido = $this->partidoDataMapper->findById(['id_partido' => $idPartido]);

        if (!$partido) {
            throw new \InvalidArgumentException("Partido $idPartido no encontrado");
        }

        $desafio = $this->desafioDataMapper->findByIdPartido($partido->getIdPartido());

        if (!$desafio) {
            throw new \InvalidArgumentException("Desafio del partido $idPartido no encontrado");
        }

        if ($desafio->getIdEquipoDesafiado() == $idEquipo) {
            return $desafio->getIdEquipoDesafiante();
        }

        return $desafio->getIdEquipoDesafiado();

    }
}
