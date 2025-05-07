<?php

namespace Paw\App\Controllers;
use Paw\App\Models\NivelElo;
use Paw\Core\AbstractController;
use Paw\App\Models\Equipo;
use Paw\App\Models\TipoEquipo;
use Paw\App\Models\Comentario;
use Paw\App\Models\EquipoCollection;
use Paw\Core\Middelware\AuthMiddelware;

class EquipoController extends AbstractController{

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

        $equipoExistente =$this->model->select(['email' => $email]);
        if ($equipoExistente) {
            $errors[] = "Ya existe un equipo registrado con ese correo electrónico.";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Las contraseñas no coinciden.";
        }

        if (empty($telefono)) {
            $errors[] = "El teléfono es obligatorio.";
        }

        if (!preg_match("/^\+54[0-9]{10,12}$/", $telefono)) {
            $errors[] ='El número de teléfono es inválido. Ej: +542323444444';
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
    
    public function registerTeam(){   
        session_start();
        $errors = [];
        if (!isset($_SESSION['equipo_temp'])) {
            $errors[] = "Error de session, por favor intentalo nuevamente.";
            $_SESSION['errors'] = $errors;
            header('Location: /create-team');
            exit;
        }
        
        $teamName = $_POST['team-name'] ?? null;
        $teamAcronym = $_POST['team-acronym'] ?? null;
        $teamTypeId = $_POST['tipo_equipo'] ?? null;
        $teamZone = $_POST['team-zone'] ?? null;
        $teamMotto = $_POST['team-motto'] ?? null;
    
        if (
            empty($teamName) || 
            empty($teamAcronym) || 
            empty($teamTypeId) ||
            empty($teamZone)
        ) {
            $errors[] = "Por favor llená los campos obligatorios.";
            header('Location: /create-account');
            exit;
        }
        if (isset($teamZone['lat']) && isset($teamZone['lng'])) {
            $lat = floatval($teamZone['lat']);
            $lng = floatval($teamZone['lng']);
            $geolocalizacion = "POINT($lng $lat)";
        } else {
            $geolocalizacion = null;
        }

        $tipoEquipoModel = $this->getModel(TipoEquipo::class);
        $id_tipo_equipo = $tipoEquipoModel->find(['id_tipo_equipo' => $teamTypeId]);
        if (!$id_tipo_equipo) {
            $errors[] = "El tipo de equipo seleccionado no es válido.";
        }

        // Seteamos todos los valores en el modelo
        $params = [
            'email' => $_SESSION['equipo_temp']['email'],
            'contrasena' => $_SESSION['equipo_temp']['password'],
            'telefono' => $_SESSION['equipo_temp']['telefono'],
            'nombre' => $teamName,
            'acronimo' => $teamAcronym,
            'id_tipo_equipo' => $id_tipo_equipo[0]['id_tipo_equipo'],
            'ubicacion' => $geolocalizacion ?? null,
            'lema' => $teamMotto,
        ];
        
        // Guardamos en la base de datos
        $insertedId = $this->model->saveNewTeam($params);

        if ($insertedId !== null) {
            // Limpiamos la sesión temporal
            unset($_SESSION['equipo_temp']);
            header('Location: /login');
            exit;
        } else {
            $errors[] = "Hubo un error al registrar el equipo. Por favor intentalo nuevamente";
        }
    }

    public function searchTeam() {
        $nombre = $_GET['nombre'] ?? null;
        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $id_nivel_elo = isset($_GET['id_nivel_elo']) ? (int)$_GET['id_nivel_elo'] : null;
        $id_equipo = isset($_GET['id_equipo_desafiar']) ? (int)$_GET['id_equipo_desafiar'] : null;
        $porPagina = 3;
        $offset = ($paginaActual - 1) * $porPagina;

        if ($id_equipo) {
            //Armar desafío --> necesitamos el login
        }
        if ($nombre && $id_nivel_elo) {
            $todosLosEquipos = $this->model->selectLike(['nombre' => $nombre, 'id_nivel_elo' => $id_nivel_elo]);
        } elseif ($nombre){
            $todosLosEquipos = $this->model->selectLike(['nombre' => $nombre]);
        } elseif ($id_nivel_elo){
            $todosLosEquipos = $this->model->selectLike(['id_nivel_elo' => $id_nivel_elo]);
        }else{
            $todosLosEquipos = $this->model->select([]);
        }

        $totalEquipos = count($todosLosEquipos);
        $totalPaginas = ceil($totalEquipos / $porPagina);

        $equipos = array_slice($todosLosEquipos, $offset, $porPagina);

        $nivelEloModel = $this->getModel(NivelElo::class);
        $nivelesElo = $nivelEloModel->select([]);
        $comentarioModel = $this->getModel(Comentario::class);
        $comentarios = $comentarioModel->select([]);

        $deportividadPorEquipo = [];

        foreach ($comentarios as $comentario) {
            $id = $comentario['id_equipo_comentado'];
            if (!isset($deportividadPorEquipo[$id])) {
                $deportividadPorEquipo[$id] = ['total' => 0, 'cantidad' => 0];
            }
            $deportividadPorEquipo[$id]['total'] += (float)$comentario['deportividad'];
            $deportividadPorEquipo[$id]['cantidad']++;
        }

        foreach ($equipos as &$equipo) {
            $idEquipo = $equipo['id_equipo'];
            $nivelElo = $nivelEloModel->select(['id_nivel_elo' => $equipo['id_nivel_elo']]);
            $equipo['nivel_elo_descripcion'] = $nivelElo[0]['descripcion'] ?? 'Sin nivel';
            if (isset($deportividadPorEquipo[$idEquipo])) {
                $total = $deportividadPorEquipo[$idEquipo]['total'];
                $cantidad = $deportividadPorEquipo[$idEquipo]['cantidad'];
                $equipo['deportividad'] = round($total / $cantidad, 2);
            } else {
                $equipo['deportividad'] = null;
            }
        }

        require $this->viewsDir . 'search-team.php';
    }

    public function dashboard(){

        $equipo_jwt_data = AuthMiddelware::verificar();
        
        $equipo = $this->getEquipo($equipo_jwt_data->id_equipo);

        $page  = max(1, (int)($_GET['page'] ?? 1));
        $per   = 3;
        $order = $_GET['order'] ?? 'fecha_creacion';
        $dir   = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

        $comentariosPag = $equipo->getComentarios($page, $per, $order, $dir);
        $desafiosRecib  = $equipo->getDesafiosRecibidos($page, $per, $order, $dir);

        $nivelDesc    = $equipo->getNivelElo();
        $deportividad = $equipo->promediarDeportividad();



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