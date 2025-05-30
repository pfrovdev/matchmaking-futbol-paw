<?php
namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\Services\DesafioService;
use Paw\App\Services\EquipoService;
use Paw\App\Services\NotificationService;
use Paw\Core\AbstractController;
use Paw\Core\Container;
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


    public function getDesafiosPendientes(): void
    {
        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);
        $desafios = $this->desafioService->getDesafiosByEquipoAndEstadoDesafio(
            $equipo->getIdEquipo(),
            'pendiente'
        );

        require $this->viewsDir . 'desafios-pendientes.php';
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
