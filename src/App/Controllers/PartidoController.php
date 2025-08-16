<?php

namespace Paw\App\Controllers;

use Exception;
use Monolog\Logger;
use Paw\App\Dtos\BadgeEquipoFormularoDto;
use Paw\App\Dtos\FormularioEquipoDto;
use Paw\App\Dtos\FormularioPartidoDto;
use Paw\App\Enums\ProcesarFormularioEstado;
use Paw\App\Models\FormularioPartido;
use Paw\App\Services\EquipoService;
use Paw\App\Services\PartidoService;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;
use RuntimeException;

class PartidoController extends AbstractController
{
    private PartidoService $partidoService;
    private EquipoService $equipoService;
    public function __construct(Logger $logger, PartidoService $partidoService, EquipoService $equipoService, AuthMiddelware $authMiddelware)
    {
        parent::__construct($logger, $authMiddelware);
        $this->partidoService = $partidoService;
        $this->equipoService = $equipoService;
    }

    // obtiene los partidos pendientes del equipo que esta logueado (se muestra en el dashboard - renderizado en js)
    public function index()
    {
        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(20, (int) ($_GET['per_page'] ?? 3)));
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir = strtoupper($_GET['dir'] ?? 'DESC');

        $partidosPendientes = $this->partidoService->getProximosPartidos(
            $equipo->getIdEquipo(),
            $page,
            $perPage,
            $order,
            $dir
        );

        header('Content-Type: application/json');
        echo json_encode($partidosPendientes);
    }


    public function getHistorial()
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(20, (int) ($_GET['per_page'] ?? 3)));
        $order = in_array($_GET['order'] ?? 'fecha_finalizacion', ['fecha_finalizacion', 'deportividad']) ? $_GET['order'] : 'fecha_finalizacion';
        $dir = strtoupper($_GET['dir'] ?? 'DESC');
        $dir = in_array($dir, ['ASC', 'DESC']) ? $dir : 'DESC';

        $equipoId = isset($_GET['equipo_id']) ? (int) $_GET['equipo_id'] : $miEquipo->getIdEquipo();

        $this->logger->info("page $page perPage $perPage order $order dir $dir equipoId $equipoId");

        $partidosHistorial = $this->partidoService->getHistorialPartidosByIdEquipo(
            $equipoId,
            $page,
            $perPage,
            $order,
            $dir
        );

        if (!empty($partidosHistorial['data'])) {
            $this->logger->info('resultadoPaginado: ' . json_encode($partidosHistorial['data'][0]->getComentario()));
        } else {
            $this->logger->info('resultadoPaginado sin datos');
        }

        header('Content-Type: application/json');
        echo json_encode($partidosHistorial);
    }


    public function coordinarResultado(): void
    {
        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $id_partido = filter_input(INPUT_GET, 'id_partido', FILTER_VALIDATE_INT);
        if (!$id_partido || $id_partido < 1) {
            $this->renderNotFound();
            return;
        }

        list($formularioPartidoContrario, $miUltimaIteracion) =
            $this->prepararVistaCoordinar($id_partido, $miEquipo->getIdEquipo());

        if ($this->partidoService->manejarDeadlineSiCorresponde($id_partido)) {
            $formularioPartidoContrario = $this->partidoService
                ->getUltimosFormulariosEquipoContrario($id_partido, $miEquipo->getIdEquipo());
            $miUltimaIteracion = $this->partidoService
                ->getUltimaIteracion($id_partido, $miEquipo->getIdEquipo());

            if (!$formularioPartidoContrario) {
                $idEquipoRival = $this->partidoService
                    ->getEquipoRival($id_partido, $miEquipo->getIdEquipo());
                $formularioPartidoContrario = new FormularioPartidoDto(
                    $idEquipoRival,
                    $id_partido,
                    0,
                    new FormularioEquipoDto($this->equipoService->getBadgeEquipo($idEquipoRival), 0,0,0,0),
                    new FormularioEquipoDto($this->equipoService->getBadgeEquipo($miEquipo->getIdEquipo()), 0,0,0,0)
                );
            }

            $_SESSION['flash'] = [
                'mensaje'    => 'Se pasó el plazo de 48 hs, el partido se dio por finalizado automáticamente con la última carga.',
                'finalizado' => true
            ];
            $this->logger->info('>>> formularioPartidoContrario ' . 
            ($formularioPartidoContrario ? 'OK id=' . $formularioPartidoContrario->getIdPartido() : 'NULL'));

            require $this->viewsDir . 'coordinar-resultado.php';
            return;
        }

        $formularioPartidoContrario = $this->partidoService->getUltimosFormulariosEquipoContrario($id_partido, $miEquipo->getIdEquipo());
        $miUltimaIteracion = $this->partidoService->getUltimaIteracion($id_partido, $miEquipo->getIdEquipo());

        if (!$formularioPartidoContrario) {
            $idEquipoRival = $this->partidoService->getEquipoRival($id_partido, $miEquipo->getIdEquipo());
            $formularioPartidoContrario = new FormularioPartidoDto(
                $idEquipoRival,
                $id_partido,
                0,
                new FormularioEquipoDto($this->equipoService->getBadgeEquipo($idEquipoRival), 0, 0, 0, 0),
                new FormularioEquipoDto($this->equipoService->getBadgeEquipo($miEquipo->getIdEquipo()), 0, 0, 0, 0)
            );
        }

        if ($this->partidoService->partidoAcordado($miEquipo->getIdEquipo(), $id_partido)) {
            $_SESSION['flash']['mensaje'] = $this->generarMensajeEstado(ProcesarFormularioEstado::PARTIDO_TERMINADO);
            $_SESSION['flash']['finalizado'] = true;
        }
        $this->logger->info('>>> formularioPartidoContrario ' . 
        ($formularioPartidoContrario ? 'OK id=' . $formularioPartidoContrario->getIdPartido() : 'NULL'));

        require $this->viewsDir . 'coordinar-resultado.php';
    }


    private function prepararVistaCoordinar(int $id_partido, int $miEquipoId): array
    {
        $dto  = $this->partidoService
                    ->getUltimosFormulariosEquipoContrario($id_partido, $miEquipoId);
        $iter = $this->partidoService
                    ->getUltimaIteracion($id_partido, $miEquipoId);

        if (! $dto) {
            $rival     = $this->partidoService->getEquipoRival($id_partido, $miEquipoId);
            $badgeRival= $this->equipoService->getBadgeEquipo($rival);
            $badgeMio  = $this->equipoService->getBadgeEquipo($miEquipoId);
            $dto       = new FormularioPartidoDto(
                $rival,
                $id_partido,
                0,
                new FormularioEquipoDto($badgeRival, 0,0,0,0),
                new FormularioEquipoDto($badgeMio,   0,0,0,0)
            );
        }

        return [ $dto, $iter ];
    }

    private function renderNotFound(): void
    {
        header("HTTP/1.1 404 Not Found");
        require $this->viewsDir . 'errors/not-found.php';
    }


    public function enviarFormulario(): void
    {
        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $id_partido = filter_input(INPUT_GET, 'id_partido', FILTER_VALIDATE_INT);
        if (!$id_partido || $id_partido < 1) {
            $this->renderNotFound();
            return;
        }

        // Sanitizar e interpretar datos POST
        $input = filter_input_array(INPUT_POST, [
            'goles_visitante' => FILTER_VALIDATE_INT,
            'asistencias_visitante' => FILTER_VALIDATE_INT,
            'tarjetas_amarillas_visitante' => FILTER_VALIDATE_INT,
            'tarjetas_rojas_visitante' => FILTER_VALIDATE_INT,
            'goles_local' => FILTER_VALIDATE_INT,
            'asistencias_local' => FILTER_VALIDATE_INT,
            'tarjetas_amarillas_local' => FILTER_VALIDATE_INT,
            'tarjetas_rojas_local' => FILTER_VALIDATE_INT,
        ]);
        if (! is_array($input)) {
            $input = [];
        }

        // Normalizar nulos
        $defaults = [
            'goles_visitante' => 0,
            'asistencias_visitante' => 0,
            'tarjetas_amarillas_visitante' => 0,
            'tarjetas_rojas_visitante' => 0,
            'goles_local' => 0,
            'asistencias_local' => 0,
            'tarjetas_amarillas_local' => 0,
            'tarjetas_rojas_local' => 0,
        ];
        $input = array_merge($defaults, $input);

        $formularioVisitante = new FormularioPartido();
        $formularioVisitante->set([
            'id_equipo' => $miEquipo->id_equipo,
            'id_partido' => $id_partido,
            'fecha' => date('Y-m-d H:i:s'),
            'total_faltas' => 0,
            'total_goles' => $input['goles_visitante'],
            'total_asistencias' => $input['asistencias_visitante'],
            'total_amarillas' => $input['tarjetas_amarillas_visitante'],
            'total_rojas' => $input['tarjetas_rojas_visitante'],
            'tipo_formulario' => "FORMULARIO_EQUIPO_CONTRARIO"
        ]);

        $formularioLocal = new FormularioPartido();
        $formularioLocal->set([
            'id_equipo' => $miEquipo->id_equipo,
            'id_partido' => $id_partido,
            'fecha' => date('Y-m-d H:i:s'),
            'total_faltas' => 0,
            'total_goles' => $input['goles_local'],
            'total_asistencias' => $input['asistencias_local'],
            'total_amarillas' => $input['tarjetas_amarillas_local'],
            'total_rojas' => $input['tarjetas_rojas_local'],
            'tipo_formulario' => "FORMULARIO_MI_EQUIPO"
        ]);

        $estado = $this->partidoService->procesarFormulario($miEquipo->getIdEquipo(), $id_partido, $formularioLocal, $formularioVisitante);
        $_SESSION['flash'] = [
            'mensaje' => $this->generarMensajeEstado($estado),
            'finalizado' => $estado === ProcesarFormularioEstado::PARTIDO_TERMINADO
        ];

        header("Location: /coordinar-resultado?id_partido={$id_partido}");
        exit;
    }


    private function generarMensajeEstado($estado): string
    {
        $mensajeEstado = "";

        switch ($estado) {
            case ProcesarFormularioEstado::MAXIMAS_ITERACIONES_ALCANZADAS:
                $mensajeEstado = "Ya no se aceptan más iteraciones. El partido se ha finalizado automáticamente.";
                break;

            case ProcesarFormularioEstado::FUERA_DE_TURNO:
                $mensajeEstado = "Debes esperar a que el rival suba su formulario para continuar.";
                break;

            case ProcesarFormularioEstado::PARTIDO_TERMINADO:
                $mensajeEstado = "Los resultados coinciden, el partido ha sido finalizado.";
                break;

            case ProcesarFormularioEstado::NUEVA_ITERACION:
                $mensajeEstado = "Formulario guardado. El rival fue avisado de tu iteración, te avisaremos cuando el rival responda.";
                break;
        }

        return $mensajeEstado;
    }
}
