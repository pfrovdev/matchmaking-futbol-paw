<?php

namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\Services\EquipoService;
use Paw\App\Services\PartidoService;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

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
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $id_partido = isset($_GET['id_partido']) ? (int) $_GET['id_partido'] : -1;

        if($this->partidoService->validarPartido($id_partido, $equipo->getIdEquipo())){
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
        }

        // validar que el id partido está presente.

        // validar que el partido no está finalizado.

        // validar que el partido no está cancelado.

        // validar que el user principal participo en el partido.

        $datos_contrario = [
            'goles'            => 0,
            'asistencias'      => 0,
            'tarjeta_amarilla' => 0,
            'tarjeta_roja'     => 0,
        ];
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);
        require $this->viewsDir . 'coordinar-resultado.php';
    }
}
