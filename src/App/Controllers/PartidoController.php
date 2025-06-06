<?php

namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\Dtos\FormularioEquipoDto;
use Paw\App\Dtos\FormularioPartidoDto;
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

        try {
            $this->partidoService->validarPartido($id_partido, $miEquipo->getIdEquipo());
        } catch (RuntimeException $e) {
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
                    0,
                    0,
                    0,
                    0
                ),
                new FormularioEquipoDto(
                    0,
                    0,
                    0,
                    0
                )
            );
        }

        require $this->viewsDir . 'coordinar-resultado.php';
    }
}
