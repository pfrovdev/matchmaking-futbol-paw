<?php

namespace Paw\App\Controllers;

use Paw\App\Commons\NotificadorEmail;
use Paw\App\Models\NivelElo;
use Paw\Core\AbstractController;
use Paw\App\Models\Equipo;
use Paw\App\Models\TipoEquipo;
use Paw\App\Models\Comentario;
use Paw\App\Models\EquipoCollection;
use Paw\App\Models\ResultadoPartido;
use Paw\App\Utils\CalculadoraDeElo;
use Paw\Core\JWT\Auth;
use Paw\Core\Middleware\AuthMiddleware;

class EquipoControllerrr extends AbstractController{

    public ?string $modelName = Equipo::class;

    public function createAccount(){
        require $this->viewsDir . 'create-account.php';
    }

    public function createTeam(){
        $tipoEquipoModel = $this->getModel(TipoEquipo::class);
        $tipos = $tipoEquipoModel->all();
        require $this->viewsDir . 'create-team.php';
    }

    public function register(){
        session_start();
        $email = $_POST['email'] ?? null;
        $confirmEmail = $_POST['confirm-email'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;
        $telefono = $_POST['telefono'] ?? null;

        $errors = [];

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El correo electrónico no es válido.";
        }

        if ($email !== $confirmEmail) {
            $errors[] = "Los correos electrónicos no coinciden.";
        }
        if ($this->model->select(['email' => $email])) {
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

        if ($errors) {
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

    public function registerTeam(){
        if (session_status() === PHP_SESSION_NONE) session_start();
        $errors = [];

        if (!isset($_SESSION['equipo_temp'])) {
            $_SESSION['errors'] = ["Error de sesión, por favor intentalo nuevamente."];
            header('Location: /create-team');
            exit;
        }
        $equipoTemp = $_SESSION['equipo_temp'];
        unset($_SESSION['equipo_temp']);

        $teamName    = $_POST['team-name']    ?? null;
        $teamAcronym = $_POST['team-acronym'] ?? null;
        $teamTypeId  = $_POST['tipo_equipo']  ?? null;
        $lat         = $_POST['lat']          ?? null;
        $lng         = $_POST['lng']          ?? null;
        $teamMotto   = $_POST['team-motto']   ?? null;

        if (!$teamName || !$teamAcronym || !$teamTypeId || !$lat || !$lng) {
            $errors[] = "Por favor llená los campos obligatorios.";
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = array_merge($equipoTemp, $_POST);
            header('Location: /create-team');
            exit;
        }

        $geolocalizacion = "ST_GeomFromText('POINT($lng $lat)', 4326)";

        $tipoEquipoModel = $this->getModel(TipoEquipo::class);
        $tipoArr = $tipoEquipoModel->find(['id_tipo_equipo' => $teamTypeId]);
        if (!$tipoArr) {
            $errors[] = "El tipo de equipo seleccionado no es válido.";
        }
        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = array_merge($equipoTemp, $_POST);
            header('Location: /create-team');
            exit;
        }

        $params = [
            'email'           => $equipoTemp['email'],
            'contrasena'      => $equipoTemp['password'],
            'telefono'        => $equipoTemp['telefono'],
            'nombre'          => $teamName,
            'acronimo'        => $teamAcronym,
            'id_tipo_equipo'  => $tipoArr[0]['id_tipo_equipo'],
            'ubicacion'       => $geolocalizacion,
            'lema'            => $teamMotto,
            'id_nivel_elo' => 1, // Principiante
            'id_rol' => 2 // Usuario
        ];
        $insertedId = $this->model->saveNewTeam($params);

        if ($insertedId) {
            header('Location: /login');
            exit;
        }

        $_SESSION['errors'] = ["Hubo un error al registrar el equipo. Por favor intentalo nuevamente."];
        $_SESSION['old']    = array_merge($equipoTemp, $_POST);
        header('Location: /create-team');
        exit;
    }

    public function searchTeam() {
        $equipoJwtData = $this->auth->verificar(['ADMIN','USUARIO']);
        $miEquipo = $this->getEquipo($equipoJwtData->id_equipo);
        
        $latitud = isset($_GET['lat']) ? (float) $_GET['lat'] : null;
        $longitud = isset($_GET['lng']) ? (float) $_GET['lng'] : null;
        $radio_km = isset($_GET['radius_km']) ? (float) $_GET['radius_km'] : null;

        $nombre = $_GET['nombre'] ?? null;
        $id_nivel_elo = isset($_GET['id_nivel_elo']) ? (int)$_GET['id_nivel_elo'] : null;
        $id_equipo = isset($_GET['id_equipo_desafiar']) ? (int)$_GET['id_equipo_desafiar'] : null;

        $orden = $_GET['orden'] ?? 'desc';
        $orden = in_array($orden, ['asc', 'desc', 'alpha']) ? $orden : 'desc';
        
        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $porPagina = 3;
        $offset = ($paginaActual - 1) * $porPagina;
        
        if (!$miEquipo) {
            require $this->viewsDir . 'not-found.php';
        }

        // Si hay equipo desafiarlo
        if ($id_equipo) {
            $insertedId = $this->model->insertarDesafio($miEquipo->id_equipo, $id_equipo);
                
            if ($insertedId) {
                $equipoDesafiado = $this->model->getEquipo($id_equipo);
                $notificador = new NotificadorEmail();
                $notificador->enviarNotificacionDesafioCreado($equipoDesafiado, $miEquipo );
                header('Location: /dashboard');
                exit;
            }
    
            $_SESSION['errors'] = ["Hubo un error al registrar el equipo. Por favor intentalo nuevamente."];
            $_SESSION['old']    = array_merge($equipoTemp, $_POST);
            header('Location: /search-team');
            exit;
        }

        if ($orden === 'alpha') {
            $orderBy = 'nombre';
            $direction = 'ASC';
        } else {
            $orderBy = 'elo_actual';
            $direction = strtoupper($orden);
        }
        
        $selectParams = [
            'nombre' => $nombre,
            'miEquipo' => $miEquipo,
            'id_nivel_elo' => $id_nivel_elo,
            'lat' => $latitud,
            'lng' => $longitud,
            'radio_km' => $radio_km
        ];
        $todosLosEquipos = $this->model->getTeams($selectParams, $orderBy, $direction);

        $totalEquipos = count($todosLosEquipos);
        $totalPaginas = ceil($totalEquipos / $porPagina);
        $equipos = array_slice($todosLosEquipos, $offset, $porPagina);

        require $this->viewsDir . 'search-team.php';
    }

    public function dashboard(){

        $equipoJwtData = $this->auth->verificar(['ADMIN','USUARIO']);
        $miEquipo = $this->getEquipo($equipoJwtData->id_equipo);

        $page  = max(1, (int)($_GET['page'] ?? 1));
        $per   = 3;
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir   = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        
        $comentariosPag = $miEquipo->getComentarios($page, $per, $order, $dir);
        $desafiosRecib  = $miEquipo->getDesafiosPendientes($page, $per, $order, $dir);
        $nivelDesc    = $miEquipo->getNivelElo();
        $deportividad = $miEquipo->promediarDeportividad();
        $cantidadDeVotos = count($comentariosPag);
        $historial = false;
        
        if($miEquipo->contieneHistorial()){
            $ultimoPartidoJugado = $miEquipo->getHistorialPartidos(1,1)[0];
            $soyGanador = $ultimoPartidoJugado->soyEquipoGanador($miEquipo);
            $equipoLocal  = $miEquipo;
            $equipoRival  = $ultimoPartidoJugado->getEquipoRival($equipoLocal);
            $eloChange = CalculadoraDeElo::calcularCambioElo($ultimoPartidoJugado, $equipoLocal);
            $historial = true;
        }

        require $this->viewsDir . 'dashboard.php';
    }

    // obtiene el equipo que le pertenece a la persona que se logeo
    private function getEquipo(int $id_equipo): Equipo {

        $equipoCollection = $this->getModel(EquipoCollection::class);

        $equipo_data_bd = $equipoCollection->getById($id_equipo)[0];

        $equipo = $this->getModel(Equipo::class);

        $equipo->set($equipo_data_bd);

        return $equipo;
    }
    
}
?>