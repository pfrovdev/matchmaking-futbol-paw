<?php

namespace Paw\App\Controllers;

use Paw\Core\JWT\Services\TokenService;
use Paw\Core\AbstractController;
use Paw\Core\JWT\JsonFileStorage;
use Paw\App\Models\Equipo;

class AuthController extends AbstractController
{
    private TokenService $tokenService;

    public function __construct($log, $container)
    {
        parent::__construct($log, $container);
        $storage = new JsonFileStorage(__DIR__ . '/../../Core/JWT/blacklist.json');
        $this->tokenService = new TokenService($storage);
    }

    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $model    = $this->getModel(Equipo::class);
        $users    = $model->select(['email' => $email]);

        if ($users && password_verify($password, $users[0]['contrasena'])) {
            $user = $users[0];
            $data = [
                'id_equipo' => $user['id_equipo'],
                'email'     => $user['email'],
                'role'      => $user['id_rol'] == 2 ? 'USUARIO' : 'ADMIN',
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