<?php

namespace Paw\App\Controllers;
use Throwable;
use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;
use Paw\App\Models\Estadisticas;
use Paw\App\Services\EquipoService;
use Paw\App\Services\EstadisticaService;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

class EstadisticaController extends AbstractController
{

    private EquipoService $equipoService;
    private EstadisticaService $estadisticaService;

    public function __construct(
        Logger $logger,
        AuthMiddelware $auth,
        EquipoService $equipoService,
        EstadisticaService $estadisticaService
    ) {
        parent::__construct($logger, $auth);
        $this->equipoService = $equipoService;
        $this->estadisticaService = $estadisticaService;
    }

    public function showEstadisticasEquipo(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
            $idEquipo = $equipoJwtData->id_equipo ?? null;

            if (isset($_GET['id_equipo'])) {
                $idEquipo = (int) $_GET['id_equipo'];
            }

            if (!$idEquipo) {
                http_response_code(400);
                echo json_encode(['error' => 'No se proporcionó un id_equipo válido']);
                return;
            }

            $miEquipo = $this->equipoService->getEquipoById($idEquipo);

            if (!$miEquipo) {
                http_response_code(404); // Not Found
                echo json_encode(['error' => "El equipo con id {$idEquipo} no existe"]);
                return;
            }

            $estadistica = $this->estadisticaService->findEstadisticasByIdEquipo($miEquipo->getIdEquipo());

            if (!$estadistica) {
                http_response_code(204); // No Content
                echo json_encode(['message' => 'El equipo no tiene estadísticas']);
                return;
            }

            echo json_encode($estadistica);

        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Error interno del servidor',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

}