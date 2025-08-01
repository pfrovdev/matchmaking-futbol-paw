<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\EstaditicasDataMapper;
use Paw\App\DataMapper\PartidoDataMapper;
use Paw\App\DataMapper\EstadoPartidoDataMapper;
use Paw\App\Dtos\PartidoDto;
use Paw\App\Models\Equipo;
use Paw\App\Models\Estadisticas;
use Paw\App\Models\ResultadoPartido;
use Paw\App\Services\PartidoService;
use Paw\App\Models\Partido;
use Paw\App\Models\Desafio;
use DateTime;
use Paw\App\DataMapper\DesafioDataMapper;
use Paw\App\DataMapper\FormularioPartidoDataMapper;
use Paw\App\DataMapper\HistorialPartidoDataMapper;
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
    private EstaditicasDataMapper $estadisticasDataMapper;
    public function __construct(
        PartidoDataMapper $partidoDataMapper,
        EstadoPartidoDataMapper $estadoPartidoDataMapper,
        DesafioDataMapper $desafioDataMapper,
        EquipoService $equipoService,
        HistorialPartidoDataMapper $historialDataMapper,
        ResultadoPartidoDataMapper $resultadoPartidoDataMapper,
        FormularioPartidoDataMapper $formularioPartidoDataMapper,
        NotificationService $notificationService,
        EstaditicasDataMapper $estadisticasDataMapper
    ) {
        $this->partidoDataMapper = $partidoDataMapper;
        $this->estadoPartidoDataMapper = $estadoPartidoDataMapper;
        $this->desafioDataMapper = $desafioDataMapper;
        $this->equipoService = $equipoService;
        $this->historialDataMapper = $historialDataMapper;
        $this->resultadoPartidoDataMapper = $resultadoPartidoDataMapper;
        $this->formularioPartidoDataMapper = $formularioPartidoDataMapper;
        $this->notificationService = $notificationService;
        $this->estadisticasDataMapper = $estadisticasDataMapper;
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
        if (!$p) {
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
        if (!$p) {
            throw new \InvalidArgumentException("Partido $partidoId no existe");
        }
        $idEstado = $this->estadoPartidoDataMapper->findIdByCode('acordado');
        $p->setIdEstadoPartido($idEstado);
        $this->partidoDataMapper->updatePartido($p);
    }

    public function terminarPartido(int $partidoId, int $idEquipo, int $idEquipoRival): void
    {
        if (!$this->partidoAcordado($idEquipo, $idEquipoRival, $partidoId)) {
            throw new \RuntimeException('El partido no está acordado');
        }

        $partido = $this->partidoDataMapper->findById(['id_partido' => $partidoId]);
        if (!$partido) {
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
        $partidosAcordados = $this->partidoDataMapper->getAll(['id_estado_partido' => $this->estadoPartidoDataMapper->findIdByCode('acordado')]);
        $totalProximosPartidos = array_merge($partidosPendientes, $partidosAcordados);
        $misPartidosPendientes = [];        
        
        foreach ($totalProximosPartidos as $proximosPartidos) {
            // Obtenemos el desafio a partir del partido para obtener el equipo
            $getDesafio = $this->desafioDataMapper->findById(['id_partido' => $proximosPartidos->getIdPartido()]);
            // Filtramos los desafios que tengan como desafiado o desafiante el id_equipo en cuestión
            if (
                $getDesafio && ((int) $getDesafio->getIdEquipoDesafiante() === (int) $idEquipo ||
                    (int) $getDesafio->getIdEquipoDesafiado() === (int) $idEquipo)
            ) {

                if ($getDesafio->getIdEquipoDesafiante() !== $idEquipo && $proximosPartidos->getFinalizadoEquipoDesafiado() === 0) {
                    $equipo = $this->equipoService->getEquipoBanner(
                        $this->equipoService->getEquipoById((int) $getDesafio->getIdEquipoDesafiante())
                    );
                    $misPartidosPendientes[] = new PartidoDto(
                        $equipo,
                        $proximosPartidos->getIdPartido(),
                        $proximosPartidos->getFinalizado(),
                        $proximosPartidos->getFechaCreacion()
                    );
                } else if ($getDesafio->getIdEquipoDesafiado() !== $idEquipo && $proximosPartidos->getFinalizadoEquipoDesafiante() === 0) {
                    $equipo = $this->equipoService->getEquipoBanner(
                        $this->equipoService->getEquipoById((int) $getDesafio->getIdEquipoDesafiado())
                    );
                    $misPartidosPendientes[] = new PartidoDto(
                        $equipo,
                        $proximosPartidos->getIdPartido(),
                        $proximosPartidos->getFinalizado(),
                        $proximosPartidos->getFechaCreacion()
                    );
                }

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
            $idPartido = (int) $r['id_partido'];

            if (isset($partidosProcesados[$idPartido])) {
                continue;
            }
            $partidosProcesados[$idPartido] = true;

            $dtoLocal = $this->buildResultadoDtoPorEquipo(
                (int) $r['id_equipo_local'],
                (int) $r['total_amarillas_local'],
                (int) $r['total_rojas_local'],
                (int) $r['goles_equipo_local'],
                (int) $r['elo_inicial_local'],
                (int) $r['elo_final_local']
            );

            $dtoVisitante = $this->buildResultadoDtoPorEquipo(
                (int) $r['id_equipo_visitante'],
                (int) $r['total_amarillas_visitante'],
                (int) $r['total_rojas_visitante'],
                (int) $r['goles_equipo_visitante'],
                (int) $r['elo_inicial_visitante'],
                (int) $r['elo_final_visitante']
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
                $idGanador = (int) $r['id_equipo_ganador'];
                $idPerdedor = (int) $r['id_equipo_perdedor'];
                $idLocal = (int) $r['id_equipo_local'];
                $idVisit = (int) $r['id_equipo_visitante'];

                if ($idGanador === $idLocal) {
                    $dtoGanador = $dtoLocal;
                    $dtoPerdedor = $dtoVisitante;
                } else {
                    $dtoGanador = $dtoVisitante;
                    $dtoPerdedor = $dtoLocal;
                }

                $historialDtos[] = new HistorialPartidoDto(
                    $r['fecha_finalizacion'],
                    $dtoPerdedor,
                    $dtoGanador,
                    false
                );
            }
        }

        $total = $this->historialDataMapper->countByEquipo($idEquipo);
        $meta = [
            'totalItems' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => (int) ceil($total / $perPage),
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

        if ($desafio->getIdEquipoDesafiado() !== $Idequipo && $desafio->getIdEquipoDesafiante() !== $Idequipo) {
            throw new \RuntimeException("El equipo {$Idequipo} no participó en el partido");
        }

        $formularioPartido = $this->formularioPartidoDataMapper->findByIdFormularioPartido($idPartido);
        
        if ($formularioPartido->getTotalIteraciones() == 5) {
            throw new \RuntimeException("El formulario llegó a su máximo de iteraciones");
        }

        $dl = $partido->getDeadlineFormulario();
        if ($dl !== null && new DateTime() > new DateTime($dl)) {
            $this->finalizarPartido($idPartido);
            throw new RuntimeException("Se pasó el plazo de 48 hs, el partido se dio por acordado.");
        }

        return true;
    }

    public function estaFinalizado($idPartido, $miEquipo): bool
    {
        $partido = $this->partidoDataMapper->findByIdAndFinalizado($idPartido, false);
        $desafio = $this->desafioDataMapper->findByIdPartido($idPartido);

        if ($desafio->getIdEquipoDesafiado() === $miEquipo) {
            return $partido->getFinalizadoEquipoDesafiado() === 1;
        }
        if ($desafio->getIdEquipoDesafiante() !== $miEquipo) {
            return $partido->getFinalizadoEquipoDesafiante() === 1;
        }
        return false;
    }

    public function getUltimosFormulariosEquipoContrario(int $id_partido, int $id_equipo): ?FormularioPartidoDto
    {
        // 1) Traigo todos los formularios del partido, ordenados de más nuevo a más viejo
        $formularios = $this->formularioPartidoDataMapper
                        ->findByIdPartidoOrderByFechaDesc($id_partido);

        // 2) Filtro solo los del equipo contrario
        $formulariosContrario = array_filter(
            $formularios,
            fn($f) => $f->getIdEquipo() !== $id_equipo
        );

        // 3) Si no hay ninguno, devuelvo un DTO “vacío” en iteración 0
        if (empty($formulariosContrario)) {
            $idEquipoRival = $this->getEquipoRival($id_partido, $id_equipo);
            return new FormularioPartidoDto(
                $idEquipoRival,
                $id_partido,
                0,  // iteración 0
                new FormularioEquipoDto(
                    $this->equipoService->getBadgeEquipo($idEquipoRival),
                    0, 0, 0, 0
                ),
                new FormularioEquipoDto(
                    $this->equipoService->getBadgeEquipo($id_equipo),
                    0, 0, 0, 0
                )
            );
        }

        // 4) Agrupo por iteración y obtengo la máxima
        $porIteracion = [];
        foreach ($formulariosContrario as $f) {
            $porIteracion[$f->getTotalIteraciones()][] = $f;
        }
        $ultimaIteracion   = max(array_keys($porIteracion));
        $formulariosUltima = $porIteracion[$ultimaIteracion];

        // 5) Localizo los dos formularios de esa iteración
        $mi     = null;
        $contra = null;
        foreach ($formulariosUltima as $f) {
            if ($f->getTipoFormulario() === 'FORMULARIO_MI_EQUIPO') {
                $mi = $f;
            } else {
                $contra = $f;
            }
        }

        // 6) Armo y devuelvo el DTO
        return new FormularioPartidoDto(
            $mi->getIdEquipo(),
            $id_partido,
            $ultimaIteracion,
            new FormularioEquipoDto(
                $this->equipoService->getBadgeEquipo($mi->getIdEquipo()),
                $mi->getTotalGoles()      ?? 0,
                $mi->getTotalAsistencias()?? 0,
                $mi->getTotalAmarillas()  ?? 0,
                $mi->getTotalRojas()      ?? 0
            ),
            new FormularioEquipoDto(
                $this->equipoService->getBadgeEquipo($id_equipo),
                $contra->getTotalGoles()      ?? 0,
                $contra->getTotalAsistencias()?? 0,
                $contra->getTotalAmarillas()  ?? 0,
                $contra->getTotalRojas()      ?? 0
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
        // 0) Recupero partido y desafío
        $p            = $this->partidoDataMapper->findById(['id_partido' => $idPartido]);
        $equipoRivalId = $this->getEquipoRival($idPartido, $idEquipo);
    
        
    
        // 1) Determino iteraciones
        $iteracionActual = $this->getUltimaIteracion($idPartido, $idEquipo) + 1;
        $iteracionRival  = $this->getUltimaIteracion($idPartido, $equipoRivalId);
    
        // 2) Primera iteración: seteo deadline a +2 días
        if ($iteracionActual === 1) {
            $deadline = (new DateTime())->modify('+2 days')->format('Y-m-d H:i:s');
            $p->setDeadlineFormulario($deadline);
            $this->partidoDataMapper->updatePartido($p);
        }
    
        // 3) Límite iteraciones
        if ($iteracionActual > 5) {
            $this->finalizarPartido($idPartido);
            return ProcesarFormularioEstado::MAXIMAS_ITERACIONES_ALCANZADAS;
        }
    
        // 4) Fuera de turno
        if ($iteracionActual > $iteracionRival + 1) {
            return ProcesarFormularioEstado::FUERA_DE_TURNO;
        }
    
        // Actualizo iteración en los objetos
        $formularioLocal->setTotalIteraciones($iteracionActual);
        $formularioVisitante->setTotalIteraciones($iteracionActual);
    
        // 5) Mismo turno: verifico coincidencia
        if ($iteracionActual === $iteracionRival) {
            $formulariosRival = $this->getUltimosFormulariosEquipoContrario($idPartido, $idEquipo);
            if ($this->formulariosCoinciden($formulariosRival, $formularioLocal, $formularioVisitante)) {
                // Marco partido como acordado
                $this->acordarPartido($idPartido);
    
                // Notificación de partido finalizado
                $this->notificationService->notifyParitdoFinalizado(
                    $this->equipoService->getEquipoById($idEquipo),
                    $this->equipoService->getEquipoById($equipoRivalId),
                    $formularioLocal,
                    $formularioVisitante
                );
    
                // Guardo formularios
                $this->formularioPartidoDataMapper->save($formularioLocal);
                $this->formularioPartidoDataMapper->save($formularioVisitante);
    
                // Unifico finalización
                $this->finalizarConFormularios($formularioLocal, $formularioVisitante);
    
                return ProcesarFormularioEstado::PARTIDO_TERMINADO;
            }
        }
    
        // 6) Nueva iteración válida
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

    private function finalizarConFormularios(FormularioPartido $formularioLocal, FormularioPartido $formularioVisitante): void
{
    $fecha_jugado = (new DateTime())->format('Y-m-d H:i:s');

    $resultadoPartido = new ResultadoPartido();

    $desafioPorIdPartido = $this->desafioDataMapper->findByIdPartido($formularioLocal->getIdPartido());

    $equipoLocal = $this->equipoService->getEquipoById((int) $desafioPorIdPartido->getIdEquipoDesafiante());
    $equipoVisitante = $this->equipoService->getEquipoById((int) $desafioPorIdPartido->getIdEquipoDesafiado());
    $elo_inicial_local = $equipoLocal->getEloActual();
    $elo_inicial_visitante = $equipoVisitante->getEloActual();

    $K = 40;

    $expectativa_local = 1 / (1 + pow(10, ($elo_inicial_visitante - $elo_inicial_local) / 400));
    $expectativa_visitante = 1 - $expectativa_local;
    $golesLocal = $formularioLocal->getTotalGoles();
    $golesVisitante = $formularioVisitante->getTotalGoles();

    if ($golesLocal > $golesVisitante) {
        $resultado = "gano_local";
        $score_local = 1;
        $score_visitante = 0;
    } elseif ($golesLocal < $golesVisitante) {
        $resultado = "gano_visitante";
        $score_local = 0;
        $score_visitante = 1;
    } else {
        $resultado = "empate";
        $score_local = 0.5;
        $score_visitante = 0.5;
    }

    $elo_final_local = round($elo_inicial_local + $K * ($score_local - $expectativa_local));
    $elo_final_visitante = round($elo_inicial_visitante + $K * ($score_visitante - $expectativa_visitante));

    $resultadoPartido->set([
        "id_partido" => $formularioLocal->getIdPartido(),
        "id_equipo_local" => $equipoLocal->getIdEquipo(),
        "id_equipo_visitante" => $equipoVisitante->getIdEquipo(),
        "goles_equipo_local" => $formularioLocal->getTotalGoles(),
        "goles_equipo_visitante" => $formularioVisitante->getTotalGoles(),
        "elo_inicial_local" => $elo_inicial_local,
        "elo_final_local" => $elo_final_local,
        "elo_inicial_visitante" => $elo_inicial_visitante,
        "elo_final_visitante" => $elo_final_visitante,
        "total_amarillas_local" => $formularioLocal->getTotalAmarillas(),
        "total_amarillas_visitante" => $formularioVisitante->getTotalAmarillas(),
        "total_rojas_local" => $formularioLocal->getTotalRojas(),
        "total_rojas_visitante" => $formularioVisitante->getTotalRojas(),
        "total_asistencias_local" => $formularioLocal->getTotalAsistencias(),
        "total_asistencias_visitante" => $formularioVisitante->getTotalAsistencias(),
        "fecha_jugado" => $fecha_jugado,
        "resultado" => $resultado
    ]);

    $equipoLocal->setEloActual($elo_final_local);
    $equipoVisitante->setEloActual($elo_final_visitante);
    $this->equipoService->ActualizarEloActualEquipo($equipoLocal);
    $this->equipoService->ActualizarEloActualEquipo($equipoVisitante);

    $estaditicasEquipoLocal = $this->estadisticasDataMapper->findIdByIdEquipo($equipoLocal->getIdEquipo());
    $estaditicasEquipoVisitante = $this->estadisticasDataMapper->findIdByIdEquipo($equipoVisitante->getIdEquipo());
    $primerasEstadisticasLocal = false;
    $primerasEstadisticasVisitante = false;
    if ($estaditicasEquipoLocal == null) {
        $primerasEstadisticasLocal = true;
        $estaditicasEquipoLocal = new Estadisticas();
        $estaditicasEquipoLocal->setIdEquipo($equipoLocal->getIdEquipo());
        $estaditicasEquipoLocal->setGoles(0);
        $estaditicasEquipoLocal->setAsistencias(0);
        $estaditicasEquipoLocal->setTarjetasRojas(0);
        $estaditicasEquipoLocal->setTarjetasAmarillas(0);
        $estaditicasEquipoLocal->setJugados(0);
        $estaditicasEquipoLocal->setGanados(0);
        $estaditicasEquipoLocal->setPerdidos(0);
        $estaditicasEquipoLocal->setEmpatados(0);
    }
    if ($estaditicasEquipoVisitante == null) {
        $primerasEstadisticasVisitante = true;
        $estaditicasEquipoVisitante = new Estadisticas();
        $estaditicasEquipoVisitante->setIdEquipo($equipoVisitante->getIdEquipo());
        $estaditicasEquipoVisitante->setGoles(0);
        $estaditicasEquipoVisitante->setAsistencias(0);
        $estaditicasEquipoVisitante->setTarjetasRojas(0);
        $estaditicasEquipoVisitante->setTarjetasAmarillas(0);
        $estaditicasEquipoVisitante->setJugados(0);
        $estaditicasEquipoVisitante->setGanados(0);
        $estaditicasEquipoVisitante->setPerdidos(0);
        $estaditicasEquipoVisitante->setEmpatados(0);
    }

    // Estadísticas local
    $estaditicasEquipoLocal->setGoles($estaditicasEquipoLocal->getGoles() + $golesLocal);
    $estaditicasEquipoLocal->setAsistencias($estaditicasEquipoLocal->getAsistencias() + $formularioLocal->getTotalAsistencias());
    $estaditicasEquipoLocal->setTarjetasRojas($estaditicasEquipoLocal->getTarjetasRojas() + $formularioLocal->getTotalRojas());
    $estaditicasEquipoLocal->setTarjetasAmarillas($estaditicasEquipoLocal->getTarjetasAmarillas() + $formularioLocal->getTotalAmarillas());
    $estaditicasEquipoLocal->setJugados($estaditicasEquipoLocal->getJugados() + 1);

    // Estadísticas visitante
    $estaditicasEquipoVisitante->setGoles($estaditicasEquipoVisitante->getGoles() + $golesVisitante);
    $estaditicasEquipoVisitante->setAsistencias($estaditicasEquipoVisitante->getAsistencias() + $formularioVisitante->getTotalAsistencias());
    $estaditicasEquipoVisitante->setTarjetasRojas($estaditicasEquipoVisitante->getTarjetasRojas() + $formularioVisitante->getTotalRojas());
    $estaditicasEquipoVisitante->setTarjetasAmarillas($estaditicasEquipoVisitante->getTarjetasAmarillas() + $formularioVisitante->getTotalAmarillas());
    $estaditicasEquipoVisitante->setJugados($estaditicasEquipoVisitante->getJugados() + 1);

    if ($resultado == "gano_local") {
        $estaditicasEquipoLocal->setGanados($estaditicasEquipoLocal->getGanados() + 1);
        $estaditicasEquipoVisitante->setPerdidos($estaditicasEquipoVisitante->getPerdidos() + 1);
    }
    if ($resultado == "gano_visitante") {
        $estaditicasEquipoLocal->setPerdidos($estaditicasEquipoLocal->getPerdidos() + 1);
        $estaditicasEquipoVisitante->setGanados($estaditicasEquipoVisitante->getGanados() + 1);

    }
    if ($resultado == "empate") {
        $estaditicasEquipoLocal->setEmpatados($estaditicasEquipoLocal->getEmpatados() + 1);
        $estaditicasEquipoVisitante->setEmpatados($estaditicasEquipoVisitante->getEmpatados() + 1);
    }
    if ($primerasEstadisticasLocal){
        $this->estadisticasDataMapper->save($estaditicasEquipoLocal);
    }else{
        $this->estadisticasDataMapper->updateEstadisticas($estaditicasEquipoLocal);
    }

    if ($primerasEstadisticasVisitante){
        $this->estadisticasDataMapper->save($estaditicasEquipoVisitante);
    }else{
        $this->estadisticasDataMapper->updateEstadisticas($estaditicasEquipoVisitante);
    }
    
    $this->resultadoPartidoDataMapper->save($resultadoPartido);
}

    public function partidoAcordado(int $idEquipo, int $idEquipoRival, int $idPartido): bool
    {
        if ($this->validarPartido($idPartido, $idEquipo) && $this->validarPartido($idPartido, $idEquipoRival)) {
            $partido = $this->partidoDataMapper->findById(['id_partido' => $idPartido]);
            return $partido->getIdEstadoPartido() == $this->estadoPartidoDataMapper->findIdByCode('acordado') && $this->estaFinalizado($idPartido, $idEquipo); // y mi equipo no tenga el finalizado en true
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

    public function manejarDeadlineSiCorresponde(int $idPartido): bool
    {
        $p = $this->partidoDataMapper->findById(['id_partido' => $idPartido]);
        $dl = $p->getDeadlineFormulario();
        if ($dl && new DateTime() > new DateTime($dl)) {
            // tomo el único formulario ingresado a tiempo (última iteración)
            $todos   = $this->formularioPartidoDataMapper
                            ->findByIdPartidoOrderByFechaDesc($idPartido);
            $unico   = reset($todos);
            $rivalId = $this->getEquipoRival($idPartido, $unico->getIdEquipo());

            // armo “fantasma” del rival
            $fantasma = new FormularioPartido();
            $fantasma->set([
                'id_equipo'       => $rivalId,
                'id_partido'      => $idPartido,
                'fecha'           => (new DateTime())->format('Y-m-d H:i:s'),
                'total_goles'     => 0,
                'total_asistencias'=> 0,
                'total_amarillas' => 0,
                'total_rojas'     => 0,
                'tipo_formulario' => $unico->getTipoFormulario() === 'FORMULARIO_MI_EQUIPO'
                                    ? 'FORMULARIO_EQUIPO_CONTRARIO'
                                    : 'FORMULARIO_MI_EQUIPO'
            ]);

            // marco partido como acordado y guardo
            $this->acordarPartido($idPartido);
            $this->formularioPartidoDataMapper->save($fantasma);
            $this->finalizarConFormularios($unico, $fantasma);

            return true;
        }
        return false;
    }

    public function getPartidoById(int $idPartido): ?Partido
    {
        return $this->partidoDataMapper->findById(['id_partido' => $idPartido]);
    }
}
