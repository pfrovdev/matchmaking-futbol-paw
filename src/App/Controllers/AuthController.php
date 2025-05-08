<?php

namespace Paw\App\Controllers;

use Paw\Core\AbstractController;
use Paw\Core\JWT\Auth;
use Paw\App\Models\Equipo;

class AuthController extends AbstractController
{
    public function login()
    {
        session_start();

        $email = trim($_POST['email'] ?? '');

        $password = $_POST['password'] ?? '';

        $equipoModel = $this->getModel(Equipo::class);
        $users = $equipoModel->select(['email' => $email]);

        if (
            is_array($users) &&
            count($users) > 0 &&
            password_verify($password, $users[0]['contrasena'])
        ) {
            $user = $users[0];

            $payloadUsuario = [
                'id_equipo' => $user['id_equipo'],
                'email'     => $user['email'],
                'role'      => $user['id_rol'] == 2 ? 'USUARIO' : 'ADMIN',
            ];
            $token = Auth::generarToken($payloadUsuario);

            setcookie(
                'paw_token',
                $token,
                [
                    'expires'  => time() + getenv('JWT_EXP_SEGUNDOS'),
                    'path'     => '/',
                    'secure'   => false,          // en producción poner true con HTTPS
                    'httponly' => true,
                    'samesite' => 'Strict',
                ]
            );

            header('Location: /dashboard');
            exit;
        }

        $_SESSION['errors'] = ['Credenciales inválidas'];
        header('Location: /login');
        exit;
    }
}