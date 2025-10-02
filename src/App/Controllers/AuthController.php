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
    private EquipoService $equipoService;

    public function __construct(Logger $logger, TokenService $tokenService, EquipoService $equipoService, AuthMiddelware $auth)
    {
        parent::__construct($logger, $auth);
        $this->tokenService = $tokenService;
        $this->equipoService = $equipoService;
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = $this->equipoService->getByEmail($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            $_SESSION['errors'] = ['Datos inválidos'];
            header('Location: /login');
            exit;
        }
        if ($user && password_verify($password, $user->getContrasena())) {
            $data = [
                'id_equipo' => $user->getIdEquipo(),
                'email' => $user->getEmail(),
                'role' => $user->getIdRol() == 2 ? 'USUARIO' : 'ADMIN',
            ];
            
            $accessTtl = (int) getenv('JWT_ACCESS_TTL');
            $refreshTtl = (int) getenv('JWT_REFRESH_TTL');

            $accessJwt = $this->tokenService->createAccessToken($data, $accessTtl);
            $refreshJwt = $this->tokenService->createRefreshToken($data, $refreshTtl);

            $cookieSecure = getenv('APP_ENV') === 'production';
            $cookieOptions = [
                'path' => '/',
                'httponly' => true,
                'secure' => $cookieSecure,
                'samesite' => 'Lax',
            ];

            setcookie('access_token', $accessJwt, array_merge($cookieOptions, ['expires' => time() + $accessTtl]));
            setcookie('refresh_token', $refreshJwt, array_merge($cookieOptions, ['expires' => time() + $refreshTtl]));

            header('Location: /dashboard');
            exit;
        }

        $_SESSION['errors'] = ['Credenciales inválidas'];
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        foreach (['access_token', 'refresh_token'] as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                $pl = $this->tokenService->decodeToken($_COOKIE[$cookie]);
                if ($pl)
                    $this->tokenService->revokeToken($pl->jti, $pl->exp);
            }
            setcookie($cookie, '', ['expires' => time() - 3600, 'path' => '/']);
        }
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function refresh(): void
    {
        $refreshJwt = $_COOKIE['refresh_token'] ?? null;
        if (!$refreshJwt) {
            http_response_code(401);
            echo json_encode(['error' => 'No refresh token']);
            exit;
        }

        $accessTtl = (int) getenv('JWT_ACCESS_TTL');
        $refreshTtl = (int) getenv('JWT_REFRESH_TTL');

        $tokens = $this->tokenService->refreshTokens($refreshJwt, $accessTtl, $refreshTtl);
        if (!$tokens) {
            http_response_code(401);
            echo json_encode(['error' => 'Refresh invalido']);
            exit;
        }

        $cookieSecure = getenv('APP_ENV') === 'production';
        $cookieOptions = [
            'path' => '/',
            'httponly' => true,
            'secure' => $cookieSecure,
            'samesite' => 'Lax',
        ];

        setcookie('access_token', $tokens['access'], array_merge($cookieOptions, ['expires' => time() + $accessTtl]));
        setcookie('refresh_token', $tokens['refresh'], array_merge($cookieOptions, ['expires' => time() + $refreshTtl]));

        echo json_encode(['ok' => true]);
        exit;
    }
}
