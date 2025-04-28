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

    public function register()
    {
        global $connection;

        $email = $_POST['email'] ?? null;
        $confirmEmail = $_POST['confirm-email'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;
        $telefono = $_POST['telefono'] ?? null;

        if (!$email || !$confirmEmail || !$password || !$confirmPassword || !$telefono) {
            echo "Faltan datos obligatorios.";
            return;
        }

        if ($email !== $confirmEmail) {
            echo "Los correos electrónicos no coinciden.";
            return;
        }

        if ($password !== $confirmPassword) {
            echo "Las contraseñas no coinciden.";
            return;
        }

        $sql = "SELECT * FROM Equipo WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$email]);
        $equipo = $stmt->fetch();

        if ($equipo) {
            echo "El correo ya está registrado.";
            return;
        }

        echo "El email está disponible. Podés seguir con el registro.";
        
    }

}

?>