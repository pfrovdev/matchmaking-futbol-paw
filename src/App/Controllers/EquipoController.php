<?php

namespace Paw\App\Controllers;

use Paw\Core\AbstractController;
use Paw\App\Models\Equipo;

class EquipoController extends AbstractController{

    public ?string $modelName = Equipo::class;
    
    public function show() {
        $id = $_GET['id'];
        $team = $this->model->getById($id);
        if(is_null($team)){
            require $this->viewsDir . 'not-found.php';
            exit;
        }
        require $this->viewsDir . '';
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
        $teamType = $_POST['team-type'] ?? null;
        $teamZone = $_POST['team-zone'] ?? null;
        $teamMotto = $_POST['team-motto'] ?? null;
    
        if (
            empty($teamName) || 
            empty($teamAcronym) || 
            empty($teamType) ||
            empty($teamZone)
        ) {
            $errors[] = "Por favor llená los campos obligatorios.";
            header('Location: /create-team');
            exit;
        }
        if (isset($teamZone['lat']) && isset($teamZone['lng'])) {
            $lat = floatval($teamZone['lat']);
            $lng = floatval($teamZone['lng']);
            $geolocalizacion = "POINT($lng $lat)";
        } else {
            $geolocalizacion = null;
        }

        // Seteamos todos los valores en el modelo
        $params = [
            'email' => $_SESSION['equipo_temp']['email'],
            'password' => $_SESSION['equipo_temp']['password'],
            'telefono' => $_SESSION['equipo_temp']['telefono'],
            'nombre_equipo' => $teamName,
            'acronimo' => $teamAcronym,
            'tipo_equipo' => $teamType,
            'geolocalizacion' => $geolocalizacion ?? null,
            'descripcion_lema' => $teamMotto,
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

    public function createTeam(){
        require $this->viewsDir . 'create-team.php';
    }
}
?>