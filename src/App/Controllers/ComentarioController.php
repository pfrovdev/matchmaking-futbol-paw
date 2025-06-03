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
        $perPage = $_GET ['per_page'] ?? 3; 
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir = $_GET['dir']   ?? 'DESC';

        $this->logger->info('page ' . $page . ' perPage ' . $perPage . ' order ' . $order . ' dir ' . $dir);

        $resultadoPaginado = $this->comentarioEquipoService
            ->getComentariosByEquipoPaginated(
                $miEquipo->getIdEquipo(),
                $page,
                $perPage,
                $order,
                $dir
            );
        $this->logger->info('resultadoPaginado ' . json_encode( $resultadoPaginado) . json_encode($resultadoPaginado['data'][0]->getComentario()));
        header('Content-Type: application/json');
        echo json_encode( $resultadoPaginado);
    }
}
