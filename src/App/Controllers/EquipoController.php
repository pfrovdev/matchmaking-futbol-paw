<?php

namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\App\DataMapper\EstaditicasDataMapper;
use Paw\App\DataMapper\ResultadoPartidoDataMapper;
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
    private EstaditicasDataMapper  $estadisticasDataMapper;
    private ResultadoPartidoDataMapper  $resultadoPartidoDataMapper;

    public function __construct(Logger $logger, EquipoService $equipoService, PartidoService $partidoService, 
                                DesafioService $desafioService, NotificationService $notificationService, 
                                ComentarioEquipoService $comentarioEquipoService, AuthMiddelware $auth,
                                EstaditicasDataMapper  $estadisticasDataMapper, ResultadoPartidoDataMapper  $resultadoPartidoDataMapper)
    {
        parent::__construct($logger, $auth);
        $this->equipoService = $equipoService;
        $this->partidoService = $partidoService;
        $this->desafioService = $desafioService;
        $this->notificationService = $notificationService;
        $this->comentarioEquipoService = $comentarioEquipoService;
        $this->estadisticasDataMapper = $estadisticasDataMapper;
        $this->resultadoPartidoDataMapper = $resultadoPartidoDataMapper;
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
        if ($this->equipoService->getByTeamName($teamName)) {
            $errors[] = "Ya existe un equipo registrado con ese nombre.";
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
                "elo_actual"      => 1500,# Elo inicial
                "id_nivel_elo"    => 1,
                "id_rol"          => 2,
            ]
        );

        $savedTeam = $this->equipoService->saveNewTeam($equipo);
        if ($savedTeam) {
            $_SESSION['email'] = $equipoTemp['email'];
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

        $equipoVistoId = isset($_GET['id']) ? (int) $_GET['id'] : $miEquipo->getIdEquipo();
        $isOwner = ($equipoVistoId === $miEquipo->getIdEquipo());

        $equipoVisto = $this->equipoService->getEquipoById($equipoVistoId);

        if (!$equipoVisto) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
        }

        $cantidadDeVotos = $this->comentarioEquipoService->getCantidadDeVotosByIdEquipo($equipoVistoId);
        $estadisticas = $this->estadisticasDataMapper->findIdByIdEquipo($equipoVisto->getIdEquipo());
        if ($estadisticas) {  
            $resultadosPartidosEstadisticas = $this->resultadoPartidoDataMapper->getResultadosPartidosEstadisticas($equipoVisto->getIdEquipo());
        }
        
        $equipoBanner = $this->equipoService->getEquipoBanner($equipoVisto);
        $listLevelsElo = $this->equipoService->getAllNivelElo();
        

        require $this->viewsDir . ($isOwner ? 'dashboard.php' : 'profile.php');
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

        $listLevelsElo = $this->equipoService->getAllNivelElo();
        $todosLosEquipos = $this->equipoService->getAllEquiposBanner($selectParams, $orderBy, $direction);
        $todosLosEquipos = $this->equipoService->quitarMiEquipoDeEquipos($todosLosEquipos, $miEquipo);

        $totalEquipos = count($todosLosEquipos);
        $totalPaginas = ceil($totalEquipos / $porPagina);
        if ($totalEquipos === 0) {
            $equipos = [];
            require $this->viewsDir . 'search-team.php';
            exit;
        }
        if ($paginaActual > $totalPaginas || $paginaActual < 1) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }

        $equipos = array_slice($todosLosEquipos, $offset, $porPagina);

        require $this->viewsDir . 'search-team.php';
    }

    public function rankingTeams()
    {
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
        if ($latitud && $longitud && $radio_km) {
            $selectParams = [
                'lat'         => $latitud,
                'lng'         => $longitud,
                'radio_km'    => $radio_km
            ];
        }

        $listLevelsElo = $this->equipoService->getAllNivelElo();

        $todosLosEquipos = $this->equipoService->getAllEquiposBanner($selectParams, $orderBy, $direction);
        $todosLosEquipos = $this->equipoService->quitarMiEquipoDeEquipos($todosLosEquipos, $miEquipo);
        $todosLosEquipos = $this->equipoService->setRestultadosPartido($todosLosEquipos);

        $totalEquipos = count($todosLosEquipos);
        $totalPaginas = ceil($totalEquipos / $porPagina);
        if ($totalEquipos === 0) {
            $equipos = [];
            require $this->viewsDir . 'ranking-teams.php';
            exit;
        }

        if ($paginaActual > $totalPaginas || $paginaActual < 1) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }

        $equipos = array_slice($todosLosEquipos, $offset, $porPagina);

        require $this->viewsDir . 'ranking-teams.php';
    }


    public function detailsTeam()
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $id_equipo = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id_equipo) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }
        $todosLosEquipos = $this->equipoService->getAllEquiposBanner([], '', '');
        $todosLosEquipos = $this->equipoService->setRestultadosPartido($todosLosEquipos);
        $listLevelsElo = $this->equipoService->getAllNivelElo();
        $equipo = $this->equipoService->getEquipoById($id_equipo);
        $equipo = $this->equipoService->getAllEquiposbyId($equipo->getIdEquipo(), $todosLosEquipos);

        require $this->viewsDir . 'details-team.php';
    }

    public function updateTeam()
    {
        $this->logger->info('updateTeam $_POST: '. print_r($_POST, true));
        $equipoJwtData = $this->auth->verificar(['ADMIN','USUARIO']);
        $miEquipo      = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);
    
        $acronimoInput = trim($_POST['team-acronym'] ?? '');
        $lemaInput     = trim($_POST['team-motto']   ?? '');
        $urlInput      = trim($_POST['team-url']     ?? '');
    
        $errors = [];
    
        if ($acronimoInput !== '') {
            if (strlen($acronimoInput) > 3) {
                $errors[] = "El acrónimo no puede superar 3 caracteres.";
            }
        }

        if ($urlInput !== '') {
            if (!filter_var($urlInput, FILTER_VALIDATE_URL)) {
                $errors[] = "La URL de la foto no es válida.";
            }
            if (strlen($urlInput) > 255) {
                $_SESSION['errors'][] = "La URL es demasiado larga (máx. 255 caracteres).";
                header("Location: /dashboard?id={$miEquipo->getIdEquipo()}");
                exit;
              }
        }
    
        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /dashboard?id={$miEquipo->getIdEquipo()}");
            exit;
        }
    
        $acronimo = $acronimoInput !== '' ? $acronimoInput : $miEquipo->getAcronimo();
        $lema     = $lemaInput     !== '' ? $lemaInput     : $miEquipo->getLema();
        $url      = $urlInput      !== '' ? $urlInput      : null;
    
 
        $equipo = new Equipo();
        $equipo->setIdEquipo($miEquipo->getIdEquipo());
        $equipo->setAcronimo($acronimo);
        $equipo->setLema($lema);
        $equipo->setUrlFotoPerfil($url);
    

        if ($this->equipoService->updateTeam($equipo)) {
            header("Location: /dashboard?id={$miEquipo->getIdEquipo()}");
            exit;
        }
    
        $_SESSION['errors'] = ["No se pudo actualizar el equipo, intentá de nuevo más tarde."];
        header("Location: /dashboard?id={$miEquipo->getIdEquipo()}");
        exit;
    }    
}
