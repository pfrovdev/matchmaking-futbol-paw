<?php

namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\Services\ComentarioEquipoService;
use Paw\App\Services\EquipoService;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

class ComentarioController extends AbstractController
{
    private ComentarioEquipoService $comentarioEquipoService;
    private EquipoService $equipoService;
    public function __construct(Logger $logger, ComentarioEquipoService $comentarioEquipoService, EquipoService $equipoService, AuthMiddelware $authMiddelware)
    {
        parent::__construct($logger, $authMiddelware);
        $this->comentarioEquipoService = $comentarioEquipoService;
        $this->equipoService = $equipoService;
    }

    public function index(): void
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(20, max(1, (int) ($_GET['per_page'] ?? 3)));

        $order = in_array($_GET['order'] ?? '', ['fecha_creacion', 'deportividad']) ? $_GET['order'] : 'fecha_creacion';
        $dir = in_array(strtoupper($_GET['dir'] ?? ''), ['ASC', 'DESC']) ? strtoupper($_GET['dir']) : 'DESC';

        $equipoId = isset($_GET['equipo_id']) ? (int) $_GET['equipo_id'] : $miEquipo->getIdEquipo();

        $this->logger->info("page={$page} perPage={$perPage} order={$order} dir={$dir} equipoId={$equipoId}");

        $resultadoPaginado = $this->comentarioEquipoService->getComentariosByEquipoPaginated($equipoId, $page, $perPage, $order, $dir);

        if (!empty($resultadoPaginado['data'])) {
            $this->logger->info('comentario sample: ' . json_encode($resultadoPaginado['data'][0]->getComentario()));
        } else {
            $this->logger->info('No hay comentarios para mostrar.');
        }

        header('Content-Type: application/json');
        echo json_encode($resultadoPaginado);
    }


    public function comentarEquipoRival(): void
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);
        $idEquipoComentador = $miEquipo->getIdEquipo();

        $input = filter_input_array(INPUT_POST, [
            'idEquipoComentado' => FILTER_VALIDATE_INT,
            'comentario' => FILTER_SANITIZE_STRING,
            'deportividad' => FILTER_VALIDATE_INT
        ]);

        if (!$input['idEquipoComentado'] || $input['deportividad'] < 0 || $input['deportividad'] > 5) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos.']);
            return;
        }

        $this->comentarioEquipoService->comentarEquipoRival(
            $idEquipoComentador,
            $input['idEquipoComentado'],
            $input['deportividad'],
            trim($input['comentario'])
        );

        http_response_code(200);
        echo json_encode(['mensaje' => 'Comentario registrado.']);
    }

}
