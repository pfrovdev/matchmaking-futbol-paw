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

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = $_GET['per_page'] ?? 3;
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir = $_GET['dir']   ?? 'DESC';

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

    public function coordinarResultado(): void
    {
        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $id_partido = isset($_GET['id_partido']) ? (int) $_GET['id_partido'] : -1;

        if ($id_partido < 0) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
        }

        // ** No valida cuando el partido no le pertenece al equipo del usuario
        try {
            $this->partidoService->validarPartido($id_partido, $miEquipo->getIdEquipo());
        } catch (Exception $e) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
        }

        $formularioPartidoContrario = $this->partidoService->getUltimosFormulariosEquipoContrario($id_partido, $miEquipo->getIdEquipo());
        $miUltimaIteracion = $this->partidoService->getUltimaIteracion($id_partido, $miEquipo->getIdEquipo());

        if (!$formularioPartidoContrario) {
            $formularioPartidoContrario =  new FormularioPartidoDto(
                $this->partidoService->getEquipoRival($id_partido, $miEquipo->getIdEquipo()),
                $id_partido,
                0,
                new FormularioEquipoDto(
                    $this->equipoService->getBadgeEquipo($this->partidoService->getEquipoRival($id_partido, $miEquipo->getIdEquipo())),
                    0,
                    0,
                    0,
                    0
                ),
                new FormularioEquipoDto(
                    $this->equipoService->getBadgeEquipo($miEquipo->getIdEquipo()),
                    0,
                    0,
                    0,
                    0
                )
            );
        }

        if($this->partidoService->partidoAcordado($miEquipo->getIdEquipo(),$id_partido)){
            $_SESSION['flash']['mensaje'] = $this->generarMensajeEstado(ProcesarFormularioEstado::PARTIDO_TERMINADO);
            $_SESSION['flash']['finalizado'] = true;
        }

        require $this->viewsDir . 'coordinar-resultado.php';
    }

    public function enviarFormulario()
    {
        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $id_partido = isset($_GET['id_partido']) ? (int)$_GET['id_partido'] : -1;

        if ($id_partido < 0) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            return;
        }

        $goles_visitante = (int)($_POST['goles_visitante'] ?? 0);
        $asistencias_visitante = (int)($_POST['asistencias_visitante'] ?? 0);
        $tarjeta_amarilla_visitante = (int)($_POST['tarjetas_amarillas_visitante'] ?? 0);
        $tarjeta_roja_visitante = (int)($_POST['tarjetas_rojas_visitante'] ?? 0);
        $goles_local = (int)($_POST['goles_local'] ?? 0);
        $asistencias_local = (int)($_POST['asistencias_local'] ?? 0);
        $tarjeta_amarilla_local = (int)($_POST['tarjetas_amarillas_local'] ?? 0);
        $tarjeta_roja_local = (int)($_POST['tarjetas_rojas_local'] ?? 0);

        $formularioVisitante = new FormularioPartido();
        $formularioVisitante->set([
            'id_equipo' => $miEquipo->id_equipo,
            'id_partido' => $id_partido,
            'fecha' => date('Y-m-d H:i:s'),
            'total_faltas' => 0,
            'total_goles' => $goles_visitante,
            'total_asistencias' => $asistencias_visitante,
            'total_amarillas' => $tarjeta_amarilla_visitante,
            'total_rojas' => $tarjeta_roja_visitante,
            'tipo_formulario' => "FORMULARIO_EQUIPO_CONTRARIO"
        ]);
        $formularioLocal = new FormularioPartido();
        $formularioLocal->set([
            'id_equipo' => $miEquipo->id_equipo,
            'id_partido' => $id_partido,
            'fecha' => date('Y-m-d H:i:s'),
            'total_faltas' => 0,
            'total_goles' => $goles_local,
            'total_asistencias' => $asistencias_local,
            'total_amarillas' => $tarjeta_amarilla_local,
            'total_rojas' => $tarjeta_roja_local,
            'tipo_formulario' => "FORMULARIO_MI_EQUIPO"
        ]);

        $estado = $this->partidoService->procesarFormulario($miEquipo->getIdEquipo(), $id_partido, $formularioLocal, $formularioVisitante);
        $mensajeEstado = $this->generarMensajeEstado($estado);
        $finalizado = $estado === ProcesarFormularioEstado::PARTIDO_TERMINADO;
        $_SESSION['flash']['mensaje'] = $mensajeEstado;
        $_SESSION['flash']['finalizado'] = $finalizado;
        header("Location: /coordinar-resultado?id_partido={$id_partido}");
        exit;
    }

    private function generarMensajeEstado($estado): String
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
