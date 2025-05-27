<?php
namespace Paw\App\Controllers;

use Paw\App\Commons\NotificadorEmail;
use Paw\App\Models\Equipo;
use Paw\App\Services\DesafioService;
use Paw\App\Services\NotificationService;
use Paw\Core\AbstractController;

class DesafioController extends AbstractController
{
    private DesafioService $desafioService;
    private NotificationService $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->desafioService = $this->getService(DesafioService::class);
        $this->notificationService = $this->getService(NotificationService::class);
    }

    public function aceptDesafio(): void
    {
        $jwtData = $this->auth->verificar(['ADMIN','USUARIO']);
        $miEquipo = $this->getEquipo($jwtData->id_equipo);

        $equipoId  = (int) ($_POST['id_equipo']  ?? 0);
        $desafioId = (int) ($_POST['id_desafio'] ?? 0);

        if ($miEquipo->fields['id_equipo'] !== $equipoId) {
            echo "No tienes permiso para aceptar este desafío";
            return;
        }

        $desafio = $this->desafioService->acceptDesafio($desafioId);

        $equipoDesafiante = $this->getEquipo(
            $desafio->fields['id_equipo_desafiante']
        );

        $this->notificationService->notifyDesafioAccepted(
            $miEquipo,
            $equipoDesafiante,
            $desafio
        );

        header("Location: /dashboard");
    }

    public function rejectDesafio(): void
    {
        $jwtData = $this->auth->verificar(['ADMIN','USUARIO']);
        $miEquipo = $this->getEquipo($jwtData->id_equipo);

        $equipoId = (int) ($_POST['id_equipo']  ?? 0);
        $desafioId = (int) ($_POST['id_desafio'] ?? 0);

        if ($miEquipo->fields['id_equipo'] !== $equipoId) {
            echo "No tienes permiso para rechazar este desafío";
            return;
        }

        $desafio = $this->desafioService->rejectDesafio($desafioId);

        $equipoDesafiante = $this->getEquipo(
            $desafio->fields['id_equipo_desafiante']
        );

        $this->notificationService->notifyDesafioRejected(
            $miEquipo,
            $equipoDesafiante,
            $desafio
        );

        header("Location: /dashboard");
    }

    private function getEquipo(int $idEquipo): Equipo
    {
        $collection = $this->getModel(\Paw\App\Models\EquipoCollection::class);
        $datosEquipo = $collection->getById($idEquipo)[0] ?? null;

        if (! $datosEquipo) {
            throw new \RuntimeException("Equipo $idEquipo no encontrado");
        }

        $equipo = $this->getModel(Equipo::class);
        $equipo->set($datosEquipo);

        return $equipo;
    }
}
