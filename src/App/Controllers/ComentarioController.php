<?php

namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\Models\Partido;
use Paw\App\Services\ComentarioEquipoService;
use Paw\App\Services\EquipoService;
use Paw\App\Services\PartidoService;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

class ComentarioController extends AbstractController
{
    private ComentarioEquipoService $comentarioEquipoService;
    private EquipoService $equipoService;
    private PartidoService $partidoService;
    public function __construct(Logger $logger, ComentarioEquipoService $comentarioEquipoService, EquipoService $equipoService, PartidoService $partidoService, AuthMiddelware $authMiddelware)
    {
        parent::__construct($logger, $authMiddelware);
        $this->comentarioEquipoService = $comentarioEquipoService;
        $this->equipoService = $equipoService;
        $this->partidoService = $partidoService;
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

        $input = [
            'idEquipoComentado' => filter_var($_POST['idEquipoComentado'] ?? null, FILTER_VALIDATE_INT),
            'id_partido' => filter_var($_POST['id_partido'] ?? null, FILTER_VALIDATE_INT),
            'deportividad' => filter_var($_POST['deportividad'] ?? null, FILTER_VALIDATE_INT),
            'comentario' => trim($_POST['comentario'] ?? ''),
        ];


        if (empty($input['idEquipoComentado']) || empty($input['id_partido']) || empty($input['deportividad']) || trim($input['comentario']) === '' || $input['deportividad'] < 0 || $input['deportividad'] > 5) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos.']);
            return;
        }

        if (! $this->partidoService->partidoAcordado(
            $idEquipoComentador,
            $input['idEquipoComentado'],
            $input['id_partido']
        )) {
            throw new \DomainException('No se puede comentar un partido no acordado');
        }

        $this->comentarioEquipoService->comentarEquipoRival(
            $idEquipoComentador,
            $input['idEquipoComentado'],
            $input['deportividad'],
            trim($input['comentario'])
        );

        //dirección POST para terminar el partido
        $this->redirectPost('/terminarPartido', [
            'id_partido' => $input['id_partido'],
            'id_equipo_rival' => $input['idEquipoComentado'],
        ]);
        exit;
    }

    function redirectPost($url, $data)
    {
        echo '<form id="redirectForm" method="POST" action="' . htmlspecialchars($url) . '">';
        foreach ($data as $name => $value) {
            echo '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '">';
        }
        echo '</form><script>document.getElementById("redirectForm").submit();</script>';
        exit;
    }
}
