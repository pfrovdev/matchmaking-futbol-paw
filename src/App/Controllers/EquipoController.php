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
    private EstaditicasDataMapper $estadisticasDataMapper;
    private ResultadoPartidoDataMapper $resultadoPartidoDataMapper;

    public function __construct(
        Logger $logger,
        EquipoService $equipoService,
        PartidoService $partidoService,
        DesafioService $desafioService,
        NotificationService $notificationService,
        ComentarioEquipoService $comentarioEquipoService,
        AuthMiddelware $auth,
        EstaditicasDataMapper $estadisticasDataMapper,
        ResultadoPartidoDataMapper $resultadoPartidoDataMapper
    ) {
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
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $confirmEmail = filter_input(INPUT_POST, 'confirm-email', FILTER_VALIDATE_EMAIL);
        $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;

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
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'telefono' => $telefono
        ];

        header('Location: /create-team');
        exit;
    }

    public function registerTeam()
    {
        if (!isset($_SESSION['equipo_temp'])) {
            $_SESSION['errors'] = ["Error de sesión, por favor intentalo nuevamente."];
            header('Location: /create-team');
            exit;
        }

        $equipoTemp = $_SESSION['equipo_temp'];
        unset($_SESSION['equipo_temp']);
        $errors = [];

        $teamName = trim($_POST['team-name'] ?? '');
        $teamAcronym = trim($_POST['team-acronym'] ?? '');
        $teamTypeId = (int) ($_POST['tipo_equipo'] ?? 0);
        $lat = filter_var($_POST['lat'] ?? null, FILTER_VALIDATE_FLOAT);
        $lng = filter_var($_POST['lng'] ?? null, FILTER_VALIDATE_FLOAT);
        $teamMotto = trim($_POST['team-motto'] ?? '');

        // Validaciones básicas
        if ($lat === false || $lng === false) {
            $errors[] = "Coordenadas inválidas.";
        }

        if (!preg_match('/^[\w\s]{3,50}$/u', $teamName)) {
            $errors[] = "El nombre del equipo contiene caracteres no permitidos o es demasiado corto.";
        }

        if (!preg_match('/^[\w\s]{2,10}$/u', $teamAcronym)) {
            $errors[] = "El acrónimo del equipo contiene caracteres no permitidos o es demasiado corto.";
        }

        if (!$teamName || !$teamAcronym || !$teamTypeId || !$lat || !$lng) {
            $errors[] = "Por favor completá todos los campos obligatorios.";
        }

        if ($this->equipoService->getByTeamName($teamName)) {
            $errors[] = "Ya existe un equipo registrado con ese nombre.";
        }

        $typeTeam = $this->equipoService->getTypeTeamById($teamTypeId);
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
        $equipo->set([
            "email" => $equipoTemp['email'],
            "contrasena" => $equipoTemp['password'],
            "telefono" => $equipoTemp['telefono'],
            "nombre" => $teamName,
            "acronimo" => $teamAcronym,
            "id_tipo_equipo" => $typeTeam->id_tipo_equipo,
            "ubicacion" => $equipo->setUbicacionFromCoords($lng, $lat),
            "lema" => $teamMotto,
            "elo_actual" => 800,
            "id_nivel_elo" => 2,
            "id_rol" => 2,
        ]);

        if ($this->equipoService->saveNewTeam($equipo)) {
            $_SESSION['email'] = $equipoTemp['email'];
            header('Location: /login');
            exit;
        }

        $_SESSION['errors'] = ["Hubo un error al registrar el equipo. Por favor intentalo nuevamente."];
        $_SESSION['old'] = array_merge($equipoTemp, $_POST);
        header('Location: /create-team');
        exit;
    }


    public function dashboard()
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $equipoVistoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: $miEquipo->getIdEquipo();
        $isOwner = ($equipoVistoId === $miEquipo->getIdEquipo());

        $equipoVisto = $this->equipoService->getEquipoById($equipoVistoId);
        if (!$equipoVisto) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }

        $cantidadDeVotos = $this->comentarioEquipoService->getCantidadDeVotosByIdEquipo($equipoVistoId);
        $estadisticas = $this->estadisticasDataMapper->findIdByIdEquipo($equipoVistoId);
        $resultadosPartidosEstadisticas = $estadisticas
            ? $this->resultadoPartidoDataMapper->getResultadosPartidosEstadisticas($equipoVistoId)
            : null;

        $equipoBanner = $this->equipoService->getEquipoBanner($equipoVisto);
        $listLevelsElo = $this->equipoService->getAllNivelElo();

        require $this->viewsDir . ($isOwner ? 'dashboard.php' : 'profile.php');
    }


    public function searchTeam()
    {
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        if (!$miEquipo) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }

        $latitud = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
        $longitud = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);
        $radio_km = filter_input(INPUT_GET, 'radius_km', FILTER_VALIDATE_FLOAT);
        $nombre = filter_input(INPUT_GET, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id_nivel_elo = filter_input(INPUT_GET, 'id_nivel_elo', FILTER_VALIDATE_INT);
        $id_equipo = filter_input(INPUT_GET, 'id_equipo_desafiar', FILTER_VALIDATE_INT);
        $orden = $_GET['orden'] ?? 'desc';
        $orden = in_array($orden, ['asc', 'desc', 'alpha']) ? $orden : 'desc';

        $paginaActual = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $paginaActual = $paginaActual && $paginaActual > 0 ? $paginaActual : 1;
        $porPagina = 3;
        $offset = ($paginaActual - 1) * $porPagina;

        if ($id_equipo) {
            try {
                if ($this->desafioService->existeDesafioPendiente($miEquipo->getIdEquipo(), $id_equipo)) {
                    $_SESSION['errors'] = ["El equipo la que intenta desafiar ya fue desafiado previamente y se encuentra en estado pendiente de aprobación."];
                    header('Location: /search-team');
                    exit;
                }

                $desafio = $this->desafioService->createDesafio($miEquipo->getIdEquipo(), $id_equipo);
                $equipoDesafiado = $this->equipoService->getEquipoById($id_equipo);
                $this->notificationService->notifyDesafioCreated($miEquipo, $equipoDesafiado, $desafio);
                header('Location: /dashboard');
                exit;
            } catch (\Exception $e) {
                $_SESSION['errors'] = ["Error al registrar el desafío. Intentá nuevamente."];
                header('Location: /search-team');
                exit;
            }
        }

        $orderBy = $orden === 'alpha' ? 'nombre' : 'elo_actual';
        $direction = $orden === 'alpha' ? 'ASC' : strtoupper($orden);

        $selectParams = [
            'nombre' => $nombre,
            'id_equipo' => $miEquipo->getIdEquipo(),
            'id_nivel_elo' => $id_nivel_elo,
            'lat' => $latitud,
            'lng' => $longitud,
            'radio_km' => $radio_km
        ];

        $listLevelsElo = $this->equipoService->getAllNivelElo();
        $todosLosEquipos = $this->equipoService->getAllEquiposBanner($selectParams, $orderBy, $direction);
        $todosLosEquipos = $this->equipoService->quitarMiEquipoDeEquipos($todosLosEquipos, $miEquipo);

        $totalEquipos = count($todosLosEquipos);
        $totalPaginas = max(1, ceil($totalEquipos / $porPagina));

        if ($paginaActual > $totalPaginas) {
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

        if (!$miEquipo) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }

        $orden = $_GET['orden'] ?? 'desc';
        $orden = in_array($orden, ['asc', 'desc', 'alpha']) ? $orden : 'asc';

        $paginaActual = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $paginaActual = $paginaActual && $paginaActual > 0 ? $paginaActual : 1;
        $porPagina = 3;
        $offset = ($paginaActual - 1) * $porPagina;

        $orderBy = $orden === 'alpha' ? 'nombre' : 'elo_actual';
        $direction = $orden === 'alpha' ? 'DESC' : strtoupper($orden);

        $selectParams = [];

        $listLevelsElo = $this->equipoService->getAllNivelElo();
        $todosLosEquipos = $this->equipoService->getAllEquiposBanner($selectParams, $orderBy, $direction);
        $todosLosEquipos = $this->equipoService->quitarMiEquipoDeEquipos($todosLosEquipos, $miEquipo);
        $todosLosEquipos = $this->equipoService->setRestultadosPartido($todosLosEquipos);

        $totalEquipos = count($todosLosEquipos);
        $totalPaginas = max(1, ceil($totalEquipos / $porPagina));

        if ($paginaActual > $totalPaginas) {
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

        $id_equipo = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id_equipo) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }

        $equipo = $this->equipoService->getEquipoById($id_equipo);
        if (!$equipo) {
            header("HTTP/1.1 404 Not Found");
            require $this->viewsDir . 'errors/not-found.php';
            exit;
        }

        $todosLosEquipos = $this->equipoService->getAllEquiposBanner([], '', '');
        $todosLosEquipos = $this->equipoService->setRestultadosPartido($todosLosEquipos);
        $listLevelsElo = $this->equipoService->getAllNivelElo();
        $estadisticas = $this->estadisticasDataMapper->findIdByIdEquipo($equipo->getIdEquipo());
        $resultadosPartidosEstadisticas = $estadisticas
            ? $this->resultadoPartidoDataMapper->getResultadosPartidosEstadisticas($equipo->getIdEquipo())
            : null;
        $equipo = $this->equipoService->getAllEquiposbyId($equipo->getIdEquipo(), $todosLosEquipos);
        require $this->viewsDir . 'details-team.php';
    }

    public function updateTeam()
    {
        $this->logger->info('updateTeam $_POST: ' . print_r($_POST, true));
        $equipoJwtData = $this->auth->verificar(['ADMIN', 'USUARIO']);
        $miEquipo = $this->equipoService->getEquipoById($equipoJwtData->id_equipo);

        $acronimoInput = trim($_POST['team-acronym'] ?? '');
        $lemaInput = trim($_POST['team-motto'] ?? '');
        $urlInput = trim($_POST['team-url'] ?? '');

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
        $lema = $lemaInput !== '' ? $lemaInput : $miEquipo->getLema();
        $url = $urlInput !== '' ? $urlInput : null;


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