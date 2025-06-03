<?php
namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\Services\DesafioService;
use Paw\App\Services\EquipoService;
use Paw\App\Services\NotificationService;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

class DesafioController extends AbstractController
{
    private DesafioService $desafioService;
    private NotificationService $notificationService;
    private EquipoService $equipoService;

    public function __construct(Logger $logger, DesafioService $desafioService, NotificationService $notificationService, EquipoService $equipoService, AuthMiddelware $authMiddelware)
    {
        parent::__construct($logger,$authMiddelware);
        $this->desafioService = $desafioService;
        $this->notificationService = $notificationService;
        $this->equipoService = $equipoService;
    }


    // obtiene los desafios pendientes del equipo que esta logueado (se muestra en el dashboard - renderizado en js)
    public function index(): void
    {
        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);
        
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = $_GET ['per_page'] ?? 3; 
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir = $_GET['dir']   ?? 'DESC';

        $desafios = $this->desafioService->getDesafiosByEquipoAndEstadoDesafio(
            $equipo->getIdEquipo(),
            'pendiente',
            $page,
            $perPage,
            $order,
            $dir
        );

        header('Content-Type: application/json');
        echo json_encode($desafios);
    }

    public function aceptarDesafio(): void
    {
        $userData = $this->auth->verificar(['ADMIN','USUARIO']);
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $desafioId = (int) ($_POST['id_desafio'] ?? 0);
        $desafio = $this->desafioService->acceptDesafio($desafioId);

        // notificar
        $desafiante = $this->equipoService->getEquipoById(
            $desafio->getIdEquipoDesafiante()
        );
        $this->notificationService->notifyDesafioAccepted(
            $equipo,
            $desafiante,
            $desafio
        );

        header('Location: /dashboard');
    }

    public function rechazarDesafio(): void
    {
        $userData = $this->auth->verificar(['ADMIN','USUARIO']);
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $desafioId = (int) ($_POST['id_desafio'] ?? 0);
        $desafio = $this->desafioService->rejectDesafio($desafioId);

        $desafiante = $this->equipoService->getEquipoById(
            $desafio->getIdEquipoDesafiante()
        );
        $this->notificationService->notifyDesafioRejected(
            $equipo,
            $desafiante,
            $desafio
        );

        header('Location: /dashboard');
    }
}
