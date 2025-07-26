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
use Paw\App\DataMapper\HistorialPartidoDataMapper;
use Paw\App\DataMapper\NivelEloDataMapper;
use Paw\App\DataMapper\ResultadoPartidoDataMapper;
use Paw\App\Dtos\FormularioEquipoDto;
use Paw\App\Dtos\FormularioPartidoDto;
use Paw\App\Dtos\HistorialPartidoDto;
use Paw\App\Dtos\ResultadoPartidoDto;
use Paw\App\Enums\ProcesarFormularioEstado;
use Paw\App\Models\FormularioPartido;
use Paw\App\Services\EquipoService;
use Paw\App\Services\NotificationService;
use RuntimeException;

class PartidoServiceImpl implements PartidoService
{
    private PartidoDataMapper $partidoDataMapper;
    private EstadoPartidoDataMapper $estadoPartidoDataMapper;
    private DesafioDataMapper $desafioDataMapper;
    private EquipoService $equipoService;
    private HistorialPartidoDataMapper $historialDataMapper;
    private ResultadoPartidoDataMapper $resultadoPartidoDataMapper;
    private FormularioPartidoDataMapper $formularioPartidoDataMapper;
    private NotificationService $notificationService;
    public function __construct(
        PartidoDataMapper $partidoDataMapper,
        EstadoPartidoDataMapper $estadoPartidoDataMapper,
        DesafioDataMapper $desafioDataMapper,
        EquipoService $equipoService,
        HistorialPartidoDataMapper $historialDataMapper,
        ResultadoPartidoDataMapper $resultadoPartidoDataMapper,
        FormularioPartidoDataMapper $formularioPartidoDataMapper,
        NotificationService $notificationService
    ) {
        $this->partidoDataMapper = $partidoDataMapper;
        $this->estadoPartidoDataMapper = $estadoPartidoDataMapper;
        $this->desafioDataMapper = $desafioDataMapper;
        $this->equipoService = $equipoService;
        $this->historialDataMapper = $historialDataMapper;
        $this->resultadoPartidoDataMapper = $resultadoPartidoDataMapper;
        $this->formularioPartidoDataMapper = $formularioPartidoDataMapper;
        $this->notificationService = $notificationService;
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
        $idFinal = $this->estadoPartidoDataMapper->findIdByCode('jugado');

        $p->finalizar($fechaFinal, $idFinal);
        $this->partidoDataMapper->updatePartido($p);
    }

    public function acordarPartido(int $partidoId): void
    {
        $p = $this->partidoDataMapper->findById(['id_partido' => $partidoId]);
        if (! $p) {
            throw new \InvalidArgumentException("Partido $partidoId no existe");
        }
        $idEstado = $this->estadoPartidoDataMapper->findIdByCode('acordado');
        $p->setIdEstadoPartido($idEstado);
        $this->partidoDataMapper->updatePartido($p);
    }

    public function terminarPartido(int $partidoId, int $idEquipo, int $idEquipoRival): void
    {
        if (! $this->partidoAcordado($idEquipo, $idEquipoRival, $partidoId)) {
            throw new \RuntimeException('El partido no está acordado');
        }

        $partido = $this->partidoDataMapper->findById(['id_partido' => $partidoId]);
        if (! $partido) {
            throw new \InvalidArgumentException("Partido {$partidoId} no existe");
        }

        $fechaFinal = (new \DateTime())->format('Y-m-d H:i:s');
        $idEstadoJugado = $this->estadoPartidoDataMapper->findIdByCode('jugado');

        if ($partido->getIdEstadoPartido() === $idEstadoJugado) {
            throw new \RuntimeException('El partido ya fue finalizado');
        }

        $partido->finalizar($fechaFinal, $idEstadoJugado);
        $this->partidoDataMapper->updatePartido($partido);
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

    public function getHistorialPartidosByIdEquipo(int $idEquipo, int $page, int $perPage, string $orderBy, string $direction): array
    {
        $offset = ($page - 1) * $perPage;

        $rows = $this->historialDataMapper->findByEquipoPaginated($idEquipo, $perPage, $offset, $orderBy, $direction);

        $historialDtos = [];
        $partidosProcesados = [];

        foreach ($rows as $r) {
            $idPartido = (int)$r['id_partido'];

            if (isset($partidosProcesados[$idPartido])) {
                continue;
            }
            $partidosProcesados[$idPartido] = true;

            $dtoLocal = $this->buildResultadoDtoPorEquipo(
                (int)$r['id_equipo_local'],
                (int)$r['total_amarillas_local'],
                (int)$r['total_rojas_local'],
                (int)$r['goles_equipo_local'],
                (int)$r['elo_inicial_local'],
                (int)$r['elo_final_local']
            );

            $dtoVisitante = $this->buildResultadoDtoPorEquipo(
                (int)$r['id_equipo_visitante'],
                (int)$r['total_amarillas_visitante'],
                (int)$r['total_rojas_visitante'],
                (int)$r['goles_equipo_visitante'],
                (int)$r['elo_inicial_visitante'],
                (int)$r['elo_final_visitante']
            );

            if ($r['resultado'] === 'empate') {
                $historialDtos[] = new HistorialPartidoDto(
                    $r['fecha_finalizacion'],
                    $dtoLocal,
                    $dtoVisitante,
                    false,
                    true
                );
            } else {
                $idGanador  = (int)$r['id_equipo_ganador'];
                $idPerdedor = (int)$r['id_equipo_perdedor'];
                $idLocal    = (int)$r['id_equipo_local'];
                $idVisit    = (int)$r['id_equipo_visitante'];

                if ($idGanador === $idLocal) {
                    $dtoGanador  = $dtoLocal;
                    $dtoPerdedor = $dtoVisitante;
                } else {
                    $dtoGanador  = $dtoVisitante;
                    $dtoPerdedor = $dtoLocal;
                }

                $soyObs = ((int)$r['id_equipo_desafiante'] === $idEquipo);

                $historialDtos[] = new HistorialPartidoDto(
                    $r['fecha_finalizacion'],
                    $dtoPerdedor,
                    $dtoGanador,
                    $soyObs,
                    false
                );
            }
        }

        $total = $this->historialDataMapper->countByEquipo($idEquipo);
        $meta  = [
            'totalItems'  => $total,
            'perPage'     => $perPage,
            'currentPage' => $page,
            'totalPages'  => (int)ceil($total / $perPage),
        ];

        return ['data' => $historialDtos, 'meta' => $meta];
    }

    private function buildResultadoDtoPorEquipo(int $idEquipo, int $amarillas, int $rojas, int $goles, int $eloInicial, int $eloFinal): ResultadoPartidoDto
    {
        $banner = $this->equipoService->getEquipoBanner($this->equipoService->getEquipoById($idEquipo));
        return new ResultadoPartidoDto(
            $banner,
            $amarillas,
            $rojas,
            $goles,
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

        // si resultado  no tiene mi equipo o equipo contrario, no se puede crear el DTO (no deberia pasar)
        if (!isset($resultado['mi_equipo']) || !isset($resultado['equipo_contrario'])) {
            throw new RuntimeException("Inconsistencia en la BD, falta un formulario de tipo mi equipo o equipo contrario en el partido con id: $id_partido iteración: $ultimaIteracion");
        }

        // se crea el DTO
        return new FormularioPartidoDto(
            $resultado['mi_equipo']->getIdEquipo(),
            $id_partido,
            $ultimaIteracion,
            new FormularioEquipoDto(
                $this->equipoService->getBadgeEquipo($resultado['mi_equipo']->getIdEquipo()),
                $resultado['mi_equipo']->getTotalGoles() ?? 0,
                $resultado['mi_equipo']->getTotalAsistencias() ?? 0,
                $resultado['mi_equipo']->getTotalAmarillas() ?? 0,
                $resultado['mi_equipo']->getTotalRojas() ?? 0
            ),
            new FormularioEquipoDto(
                $this->equipoService->getBadgeEquipo($id_equipo),
                $resultado['equipo_contrario']->getTotalGoles() ?? 0,
                $resultado['equipo_contrario']->getTotalAsistencias() ?? 0,
                $resultado['equipo_contrario']->getTotalAmarillas() ?? 0,
                $resultado['equipo_contrario']->getTotalRojas() ?? 0
            )
        );
    }

    public function getUltimaIteracion(int $idPartido, int $idEquipo): int
    {
        $formularios = $this->formularioPartidoDataMapper->findByIdPartidoAndIdEquipoOrderByFechaDesc($idPartido, $idEquipo);

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

    public function procesarFormulario(int $idEquipo, int $idPartido, FormularioPartido $formularioLocal, FormularioPartido $formularioVisitante): ProcesarFormularioEstado
    {
        // recupero los ultimos formularios del equipo contrario
        $formulariosRival = $this->getUltimosFormulariosEquipoContrario($idPartido, $idEquipo);

        // determino la iteración actual del equipo
        $iteracionActual = $this->getUltimaIteracion($idPartido, $idEquipo) + 1;

        // determino la iteración actual del rival
        $equipoRivalId = $this->getEquipoRival($idPartido, $idEquipo);
        $iteracionRival = $this->getUltimaIteracion($idPartido, $equipoRivalId);

        // si ambos llegaron al maximo
        if ($iteracionRival > 5 && $iteracionActual > 5) {
            // Avisar que el partido no se acordó
            $this->notificationService->notifyParitdoNoAcordado(
                $this->equipoService->getEquipoById($idEquipo),
                $this->equipoService->getEquipoById($equipoRivalId)
            );
            return ProcesarFormularioEstado::PARTIDO_NO_ACORDADO;
        }

        // 1) Límite de iteraciones alcanzado
        if ($iteracionActual > 5) {
            $this->finalizarPartido($idPartido);
            return ProcesarFormularioEstado::MAXIMAS_ITERACIONES_ALCANZADAS;
        }

        // 2) Fuera de turno
        if ($iteracionActual > $iteracionRival + 1) {
            return ProcesarFormularioEstado::FUERA_DE_TURNO;
        }

        // actualizo la iteración en los formularios
        $formularioLocal->setTotalIteraciones($iteracionActual);
        $formularioVisitante->setTotalIteraciones($iteracionActual);

        // 3) Mismo turno: verificar coincidencia
        if ($iteracionActual == $iteracionRival) {
            if ($this->formulariosCoinciden($formulariosRival, $formularioLocal, $formularioVisitante)) {
                $this->acordarPartido($idPartido);
                $this->notificationService->notifyParitdoFinalizado(
                    $this->equipoService->getEquipoById($idEquipo),
                    $this->equipoService->getEquipoById($equipoRivalId),
                    $formularioLocal,
                    $formularioVisitante
                );
                $this->formularioPartidoDataMapper->save($formularioLocal);
                $this->formularioPartidoDataMapper->save($formularioVisitante);
                return ProcesarFormularioEstado::PARTIDO_TERMINADO;
            }
        }

        // 4) Nueva iteración válida
        $this->notificationService->notifyNuevaIteracion(
            $this->equipoService->getEquipoById($idEquipo),
            $this->equipoService->getEquipoById($equipoRivalId),
            $iteracionActual,
            $idPartido
        );
        $this->formularioPartidoDataMapper->save($formularioLocal);
        $this->formularioPartidoDataMapper->save($formularioVisitante);
        return ProcesarFormularioEstado::NUEVA_ITERACION;
    }

    public function partidoAcordado(int $idEquipo, int $idEquipoRival, int $idPartido): bool
    {
        if ($this->validarPartido($idPartido, $idEquipo) && $this->validarPartido($idPartido, $idEquipoRival)) {
            $partido = $this->partidoDataMapper->findById(['id_partido' => $idPartido]);
            return $partido->getIdEstadoPartido() == $this->estadoPartidoDataMapper->findIdByCode('acordado');
        }
        return false;
    }

    private function formulariosCoinciden(FormularioPartidoDto $formulariosRival, FormularioPartido $formularioLocal, FormularioPartido $formularioVisitante)
    {
        return $formulariosRival->equipo_visitante->getGoles() == $formularioLocal->getTotalGoles()
            && $formulariosRival->equipo_local->getGoles() == $formularioVisitante->getTotalGoles()
            && $formulariosRival->equipo_visitante->getAsistencias() == $formularioLocal->getTotalAsistencias()
            && $formulariosRival->equipo_local->getAsistencias() == $formularioVisitante->getTotalAsistencias()
            && $formulariosRival->equipo_visitante->getTarjetasAmarilla() == $formularioLocal->getTotalAmarillas()
            && $formulariosRival->equipo_local->getTarjetasAmarilla() == $formularioVisitante->getTotalAmarillas()
            && $formulariosRival->equipo_local->getTarjetasRoja() == $formularioVisitante->getTotalRojas()
            && $formulariosRival->equipo_visitante->getTarjetasRoja() == $formularioLocal->getTotalRojas();
    }
}
