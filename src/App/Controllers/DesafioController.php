<?php

namespace Paw\App\Controllers;

use Exception;
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
        parent::__construct($logger, $authMiddelware);
        $this->desafioService = $desafioService;
        $this->notificationService = $notificationService;
        $this->equipoService = $equipoService;
    }

    private function verificarMetodoPOST(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método no permitido');
        }
    }

    private function redirigirConError(string $ruta, string $mensaje): void
    {
        $_SESSION['errors'] = [$mensaje];
        header("Location: $ruta");
        exit;
    }


    // obtiene los desafios pendientes del equipo que esta logueado (se muestra en el dashboard - renderizado en js)
    public function index(): void
    {
        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $perPage = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT) ?: 3;
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir = strtoupper($_GET['dir'] ?? 'DESC');

        // Validación de orden y dirección
        $allowedOrders = ['fecha_creacion', 'otro_campo']; // Agrega otros si existen
        if (!in_array($order, $allowedOrders))
            $order = 'fecha_creacion';

        $allowedDirs = ['ASC', 'DESC'];
        if (!in_array($dir, $allowedDirs))
            $dir = 'DESC';

        try {
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
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener los desafíos.']);
        }
    }

    public function aceptarDesafio(): void
    {
        $this->verificarMetodoPOST();

        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $desafioId = filter_input(INPUT_POST, 'id_desafio', FILTER_VALIDATE_INT);
        if (!$desafioId) {
            $this->redirigirConError('/dashboard', 'ID de desafío inválido.');
            return;
        }

        try {
            $desafio = $this->desafioService->acceptDesafio($desafioId);

            // Verificación de propiedad del desafío
            if ($desafio->getIdEquipoDesafiado() !== $equipo->getIdEquipo()) {
                throw new Exception('Acceso no autorizado al desafío.');
            }

            $desafiante = $this->equipoService->getEquipoById($desafio->getIdEquipoDesafiante());
            $this->notificationService->notifyDesafioAccepted($equipo, $desafiante, $desafio);
            header('Location: /dashboard');
        } catch (Exception $e) {
            $this->redirigirConError('/dashboard', 'Error al aceptar el desafío.');
        }
    }

    public function rechazarDesafio(): void
    {
        $this->verificarMetodoPOST();

        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $equipo = $this->equipoService->getEquipoById($userData->id_equipo);

        $desafioId = filter_input(INPUT_POST, 'id_desafio', FILTER_VALIDATE_INT);
        if (!$desafioId) {
            $this->redirigirConError('/dashboard', 'ID de desafío inválido.');
            return;
        }

        try {
            $desafio = $this->desafioService->rejectDesafio($desafioId);

            if ($desafio->getIdEquipoDesafiado() !== $equipo->getIdEquipo()) {
                throw new Exception('Acceso no autorizado.');
            }

            $desafiante = $this->equipoService->getEquipoById($desafio->getIdEquipoDesafiante());
            $this->notificationService->notifyDesafioRejected($equipo, $desafiante, $desafio);

            header('Location: /dashboard');
        } catch (Exception $e) {
            $this->redirigirConError('/dashboard', 'Error al rechazar el desafío.');
        }
    }

    public function createDesafio(): void
    {
        $this->verificarMetodoPOST();

        $userData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($userData->id_equipo);
        $referer = $_SERVER['HTTP_REFERER'] ?? '/dashboard';

        $id_equipo_desafiar = filter_input(INPUT_POST, 'id_equipo_desafiar', FILTER_VALIDATE_INT);
        if (!$id_equipo_desafiar || $id_equipo_desafiar === $miEquipo->getIdEquipo()) {
            $this->redirigirConError($referer, 'ID de equipo a desafiar inválido.');
            return;
        }

        try {
            $desafio = $this->desafioService->createDesafio($miEquipo->getIdEquipo(), $id_equipo_desafiar);
            $equipoDesafiado = $this->equipoService->getEquipoById($id_equipo_desafiar);

            $this->notificationService->notifyDesafioCreated($miEquipo, $equipoDesafiado, $desafio);
            header('Location: ' . $referer);
            exit;
        } catch (Exception $e) {
            $this->redirigirConError($referer, 'Error al crear el desafío.');
        }
    }
}
