<?php

namespace Paw\App\Controllers;

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
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);
        if (isset($_GET['id_equipo'])) {
            $miEquipo = $this->equipoService->getEquipoById((int) $_GET['id_equipo']);
        }
        $estadistica = $this->estadisticaService->findEstadisticasByIdEquipo($miEquipo->getIdEquipo());
        echo json_encode($estadistica);
    }
}
