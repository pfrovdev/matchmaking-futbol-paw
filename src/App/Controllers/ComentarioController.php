<?php

namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\Services\ComentarioEquipoService;
use Paw\App\Services\EquipoService;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

class  ComentarioController extends AbstractController
{
    private ComentarioEquipoService $comentarioEquipoService;
    private EquipoService $equipoService;
    public function __construct(Logger $logger, ComentarioEquipoService $comentarioEquipoService, EquipoService $equipoService, AuthMiddelware $authMiddelware)
    {
        parent::__construct($logger, $authMiddelware);
        $this->comentarioEquipoService = $comentarioEquipoService;
        $this->equipoService = $equipoService;
    }

    public function index()
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = $_GET['per_page'] ?? 3;
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir = $_GET['dir']  ?? 'DESC';

        $equipoId = isset($_GET['equipo_id'])
            ? (int) $_GET['equipo_id']
            : $miEquipo->getIdEquipo();

        if ($page < 1) $page = 1;
        if ($perPage < 1 || $perPage > 20) $perPage = 3;

        $allowedOrders = ['fecha_creacion', 'deportividad'];
        if (!in_array($order, $allowedOrders)) {
            $order = 'fecha_creacion';
        }
        $allowedDirs = ['ASC', 'DESC'];
        if (!in_array(strtoupper($dir), $allowedDirs)) {
            $dir = 'DESC';
        }

        $this->logger->info('page ' . $page . ' perPage ' . $perPage . ' order ' . $order . ' dir ' . $dir . ' equipoId ' . $equipoId);

        $resultadoPaginado = $this->comentarioEquipoService
            ->getComentariosByEquipoPaginated(
                $equipoId,
                $page,
                $perPage,
                $order,
                $dir
            );
            
        if (!empty($resultadoPaginado['data'])) {
            $this->logger->info('resultadoPaginado ' . json_encode($resultadoPaginado) . json_encode($resultadoPaginado['data'][0]->getComentario()));
        } else {
            $this->logger->info('resultadoPaginado sin datos');
        }

        header('Content-Type: application/json');
        echo json_encode($resultadoPaginado);
    }

    public function comentarEquipoRival()
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);
        $idEquipoComentador = $miEquipo->getIdEquipo();
        $idEquipoComentado = $_POST['idEquipoComentado'];
        $comentario = $_POST['comentario'];
        $deportividad = $_POST['deportividad'];
        $this->comentarioEquipoService->comentarEquipoRival($idEquipoComentador, $idEquipoComentado, $deportividad, $comentario);
    }
}
