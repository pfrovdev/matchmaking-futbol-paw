<?php

namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\Dtos\EquipoBannerDto;
use Paw\App\Models\Equipo;
use Paw\App\Services\ComentarioEquipoService;
use Paw\App\Services\DesafioService;
use Paw\App\Services\NotificationService;
use Paw\App\Services\EquipoService;
use Paw\App\Services\PartidoService;
use Paw\Core\AbstractController;
use Paw\Core\Middelware\AuthMiddelware;

class EquipoController extends AbstractController
{

    private EquipoService $equipoService;
    private PartidoService $partidoService;
    private DesafioService $desafioService;
    private NotificationService $notificationService;
    private ComentarioEquipoService $comentarioEquipoService;

    public function __construct(Logger $logger, EquipoService $equipoService, PartidoService $partidoService, DesafioService $desafioService, NotificationService $notificationService, ComentarioEquipoService $comentarioEquipoService, AuthMiddelware $auth)
    {
        parent::__construct($logger, $auth);
        $this->equipoService = $equipoService;
        $this->partidoService = $partidoService;
        $this->desafioService = $desafioService;
        $this->notificationService = $notificationService;
        $this->comentarioEquipoService = $comentarioEquipoService;
    }

    public function createAccount()
    {
        require $this->viewsDir . 'create-account.php';
    }

    public function createTeam()
    {
        $tipos = $this->equipoService->getAllTiposEquipos();
        require $this->viewsDir . 'create-team.php';
    }

    public function register()
    {
        $email         = $_POST['email'] ?? null;
        $confirmEmail  = $_POST['confirm-email'] ?? null;
        $password      = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;
        $telefono      = $_POST['telefono'] ?? null;

        $errors = [];

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El correo electrónico no es válido.";
        }
        if ($email !== $confirmEmail) {
            $errors[] = "Los correos electrónicos no coinciden.";
        }
        if ($this->equipoService->existsByEmail($email)) {
            $errors[] = "Ya existe un equipo registrado con ese correo electrónico.";
        }
        if ($password !== $confirmPassword) {
            $errors[] = "Las contraseñas no coinciden.";
        }
        if (empty($telefono)) {
            $errors[] = "El teléfono es obligatorio.";
        }
        if (!preg_match("/^\+54[0-9]{10,12}$/", $telefono)) {
            $errors[] = 'El número de teléfono es inválido. Ej: +542323444444';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: /create-account');
            exit;
        }

        $_SESSION['equipo_temp'] = [
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'telefono' => $telefono
        ];

        header('Location: /create-team');
        exit;
    }

    public function registerTeam()
    {
        $errors = [];

        if (!isset($_SESSION['equipo_temp'])) {
            $_SESSION['errors'] = ["Error de sesión, por favor intentalo nuevamente."];
            header('Location: /create-team');
            exit;
        }
        $equipoTemp = $_SESSION['equipo_temp'];
        unset($_SESSION['equipo_temp']);

        $teamName    = $_POST['team-name']    ?? null;
        $teamAcronym = $_POST['team-acronym']   ?? null;
        $teamTypeId  = $_POST['tipo_equipo']    ?? null;
        $lat         = $_POST['lat']            ?? null;
        $lng         = $_POST['lng']            ?? null;
        $teamMotto   = $_POST['team-motto']     ?? null;

        if (!$teamName || !$teamAcronym || !$teamTypeId || !$lat || !$lng) {
            $errors[] = "Por favor llená los campos obligatorios.";
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = array_merge($equipoTemp, $_POST);
            header('Location: /create-team');
            exit;
        }

        $typeTeam = $this->equipoService->getTypeTeamById((int)$teamTypeId);
        if (!$typeTeam) {
            $errors[] = "El tipo de equipo seleccionado no es válido.";
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = array_merge($equipoTemp, $_POST);
            header('Location: /create-team');
            exit;
        }

        $equipo = new Equipo();
        $equipo->set(
            [
                "email"           => $equipoTemp['email'],
                "contrasena"      => $equipoTemp['password'],
                "telefono"        => $equipoTemp['telefono'],
                "nombre"          => $teamName,
                "acronimo"        => $teamAcronym,
                "id_tipo_equipo"  => $typeTeam->id_tipo_equipo,
                "ubicacion"       => $equipo->setUbicacionFromCoords($lng, $lat),
                "lema"            => $teamMotto,
                "elo_actual"      => 0,
                "id_nivel_elo"    => 1,
                "id_rol"          => 2,
            ]
        );

        $savedTeam = $this->equipoService->saveNewTeam($equipo);
        if ($savedTeam) {
            header('Location: /login');
            exit;
        }

        $_SESSION['errors'] = ["Hubo un error al registrar el equipo. Por favor intentalo nuevamente."];
        $_SESSION['old']    = array_merge($equipoTemp, $_POST);
        header('Location: /create-team');
        exit;
    }

    public function dashboard()
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $page  = max(1, (int)($_GET['page'] ?? 1));
        $per   = 3;
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir   = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

        // va a cambiar para obtener via ajax
        $comentarios = $this->comentarioEquipoService->getComentariosByEquipo($miEquipo->getIdEquipo());
        $comentariosPag = array_slice($comentarios, ($page - 1) * $per, $per);
        $cantidadDeVotos = count($comentarios);

        // va a cambiar para obtener via ajax
        $desafiosRecib = $this->desafioService->getDesafiosByEquipoAndEstadoDesafio($miEquipo->getIdEquipo(), 'pendiente');

        $equipoBanner = $this->equipoService->getEquipoBanner($miEquipo);

        $historial = false;

        // ver esto 
        $historialPartidos = $this->partidoService->getHistorialPartidosByIdEquipo($miEquipo->getIdEquipo());

        if (!empty($historialPartidos)) {
            $ultimoPartidoJugado = $historialPartidos[0];
            $soyGanador = $ultimoPartidoJugado->getResultadoGanador()->getEquipo()->getIdEquipo() === $miEquipo->getIdEquipo();
            $equipoLocal  = $miEquipo;
            $equipoRival  = $this->equipoService->getEquipoById($ultimoPartidoJugado->getResultadoPerdedor()->getEquipo()->getIdEquipo());
            $eloChange = $ultimoPartidoJugado->getResultadoGanador()->getEloConseguido();
            $historial = true;
        }

        require $this->viewsDir . 'dashboard.php';
    }

    public function searchTeam()
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $latitud    = isset($_GET['lat']) ? (float) $_GET['lat'] : null;
        $longitud   = isset($_GET['lng']) ? (float) $_GET['lng'] : null;
        $radio_km   = isset($_GET['radius_km']) ? (float) $_GET['radius_km'] : null;
        $nombre     = $_GET['nombre'] ?? null;
        $id_nivel_elo = isset($_GET['id_nivel_elo']) ? (int)$_GET['id_nivel_elo'] : null;
        $id_equipo  = isset($_GET['id_equipo_desafiar']) ? (int)$_GET['id_equipo_desafiar'] : null;
        $orden      = $_GET['orden'] ?? 'desc';
        $orden      = in_array($orden, ['asc', 'desc', 'alpha']) ? $orden : 'desc';

        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $porPagina = 3;
        $offset = ($paginaActual - 1) * $porPagina;

        if (!$miEquipo) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
        }

        if ($id_equipo) {
            try {
                $desafio = $this->desafioService->createDesafio($miEquipo->getIdEquipo(), $id_equipo);
                $equipoDesafiado = $this->equipoService->getEquipoById($id_equipo);
                $this->notificationService->notifyDesafioCreated($miEquipo, $equipoDesafiado, $desafio);
                header('Location: /dashboard');
                exit;
            } catch (\Exception $e) {
                $_SESSION['errors'] = ["Hubo un error al registrar el desafío. Por favor intentalo nuevamente."];
                header('Location: /search-team');
                exit;
            }
        }

        if ($orden === 'alpha') {
            $orderBy = 'nombre';
            $direction = 'ASC';
        } else {
            $orderBy = 'elo_actual';
            $direction = strtoupper($orden);
        }

        $selectParams = [
            'nombre'      => $nombre,
            'id_equipo'    => $miEquipo->getIdEquipo(),
            'id_nivel_elo' => $id_nivel_elo,
            'lat'         => $latitud,
            'lng'         => $longitud,
            'radio_km'    => $radio_km
        ];

        $todosLosEquipos = $this->equipoService->getAllEquiposBanner($selectParams, $orderBy, $direction);
        $todosLosEquipos = $this->quitarMiEquipoDeEquipos($todosLosEquipos, $miEquipo);
        
        $totalEquipos = count($todosLosEquipos);
        $totalPaginas = ceil($totalEquipos / $porPagina);
        if ($paginaActual > $totalPaginas || $paginaActual < 1){
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }
        
        $equipos = array_slice($todosLosEquipos, $offset, $porPagina);
        
        require $this->viewsDir . 'search-team.php';
    }


    public function rankingTeams(){
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $latitud    = isset($_GET['lat']) ? (float) $_GET['lat'] : null;
        $longitud   = isset($_GET['lng']) ? (float) $_GET['lng'] : null;
        $radio_km   = isset($_GET['radius_km']) ? (float) $_GET['radius_km'] : null;
        
        $id_nivel_elo = isset($_GET['id_nivel_elo']) ? (int)$_GET['id_nivel_elo'] : null;
        $orden      = $_GET['orden'] ?? 'desc';
        $orden      = in_array($orden, ['asc', 'desc', 'alpha']) ? $orden : 'asc';
        
        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $porPagina = 3;
        $offset = ($paginaActual - 1) * $porPagina;

        if ($orden === 'alpha') {
            $orderBy = 'nombre';
            $direction = 'desc';
        } else {
            $orderBy = 'elo_actual';
            $direction = strtoupper($orden);
        }

        if (!$miEquipo) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
        }
        $selectParams = [];
        if ($id_nivel_elo) {
            $selectParams = [
                'id_nivel_elo' => $id_nivel_elo,
            ];
        }
        
        $listLevelsElo = $this->equipoService->getAllNivelElo();
        
        $todosLosEquipos = $this->equipoService->getAllEquiposBanner($selectParams, $orderBy, $direction);
        $todosLosEquipos = $this->quitarMiEquipoDeEquipos($todosLosEquipos, $miEquipo);
        $todosLosEquipos = $this->setRestultadosPartido($todosLosEquipos);
        
        $totalEquipos = count($todosLosEquipos);
        $totalPaginas = ceil($totalEquipos / $porPagina);
        if ($paginaActual > $totalPaginas || $paginaActual < 1){
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }
        
        $equipos = array_slice($todosLosEquipos, $offset, $porPagina);

        require $this->viewsDir . 'ranking-teams.php';
    }

    public function quitarMiEquipoDeEquipos(array $todosLosEquipos, Equipo $miEquipo){
        // Quitamos nuestro equipo
        $todosLosEquipos = array_filter($todosLosEquipos, function($equipo) use ($miEquipo) {
            return (int)$equipo->id_equipo !== (int)$miEquipo->id_equipo;
        });
        return $todosLosEquipos;
    }

    public function setRestultadosPartido(array $todosLosEquipos): array {
        // Recorremos y agregamos los datos solo al equipo que matchea por id
        foreach ($todosLosEquipos as &$equipo) {
            $resultadoPartidos = $this->partidoService->getResultadoPartidosByIdEquipo((int)$equipo->id_equipo);
            if ((int)$equipo->id_equipo === (int)$resultadoPartidos['id_equipo']) {
                $equipo->ganados = $resultadoPartidos['ganados'];
                $equipo->perdidos = $resultadoPartidos['perdidos'];
                $equipo->empatados = $resultadoPartidos['empatados'];
            } else {
                // Setear 0 en caso de que no sea el equipo que jugó
                $equipo->ganados = 0;
                $equipo->perdidos = 0;
                $equipo->empatados = 0;
            }
        }
    return $todosLosEquipos;
}

}