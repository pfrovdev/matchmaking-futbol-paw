<?php

namespace Paw\App\Controllers;

use Monolog\Logger;
use Paw\Core\JWT\Services\TokenService;
use Paw\Core\AbstractController;
use Paw\App\Models\Equipo;
use Paw\App\Services\EquipoService;
use Paw\Core\Middelware\AuthMiddelware;

class AuthController extends AbstractController
{
    private TokenService $tokenService;
    private EquipoService  $equipoService;

    public function __construct( Logger $logger, TokenService $tokenService, EquipoService $equipoService, AuthMiddelware $auth) {
        parent::__construct($logger, $auth);
        $this->tokenService = $tokenService;
        $this->equipoService = $equipoService;
    }

    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = $this->equipoService->getByEmail($email);

        if ($user && password_verify($password, $user->getContrasena())) {
            $data = [
                'id_equipo' => $user->getIdEquipo(),
                'email'     => $user->getEmail(),
                'role'      => $user->getIdRol() == 2 ? 'USUARIO' : 'ADMIN',
            ];

            $accessTtl  = (int) getenv('JWT_ACCESS_TTL');
            $refreshTtl = (int) getenv('JWT_REFRESH_TTL');
            $accessJwt  = $this->tokenService->createToken($data, $accessTtl);
            $refreshJwt = $this->tokenService->createToken($data, $refreshTtl);

            setcookie('access_token',  $accessJwt,  ['expires'=>time()+$accessTtl,'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Strict']);
            setcookie('refresh_token', $refreshJwt, ['expires'=>time()+$refreshTtl,'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Strict']);

            header('Location: /dashboard');
            exit;
        }

        $_SESSION['errors'] = ['Credenciales inválidas'];
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        foreach (['access_token','refresh_token'] as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                $pl = $this->tokenService->decodeToken($_COOKIE[$cookie]);
                if ($pl) $this->tokenService->revokeToken($pl->jti, $pl->exp);
            }
            setcookie($cookie, '', ['expires'=>time()-3600,'path'=>'/']);
        }
        session_destroy();
        header('Location: /login');
        exit;
    }
}