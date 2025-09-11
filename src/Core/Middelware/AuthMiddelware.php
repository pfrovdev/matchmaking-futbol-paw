<?php

namespace Paw\Core\Middelware;

use Paw\Core\JWT\Services\TokenService;
use Firebase\JWT\ExpiredException;
use Monolog\Logger;

class AuthMiddelware
{

    private TokenService $tokenService;
    private int $accessTTL;
    private int $refreshTTL;
    private Logger $logger;

    public function __construct(Logger $logger, TokenService $tokenService, int $accessTTL, int $refreshTTL)
    {
        $this->tokenService = $tokenService;
        $this->accessTTL = $accessTTL;
        $this->refreshTTL = $refreshTTL;
        $this->logger = $logger;
    }

    // verifica el token, si expiró se hace refresh
    public function verificar(array $roles = []): object
    {
        $accessToken = $_COOKIE['access_token'] ?? null;

        $payload = null;

        // Si no llego el acces token, hacemos un refresh utilizando el refresh token
        if (!$accessToken) {
            $this->logger->info("[AuthMiddelware] No se recibió access_token en la request. Comprobando refresh_token...");
            $refreshJwt = $_COOKIE['refresh_token'] ?? null;
            if ($refreshJwt) {
                $payload = $this->attemptRefreshOrFail();
            } else {
                $this->unauthorized('No autenticado (sin access_token en cookie)');
            }
        } else {
            try {
                $payload = $this->tokenService->decodeToken($accessToken);
                $this->logger->info("[AuthMiddelware] Access token decodificado", ['payload' => (array) $payload]);

                if (!$payload) {
                    $this->unauthorized('Token inválido (decode vacío)');
                }

                if (!isset($payload->typ) || $payload->typ !== 'access') {
                    $this->unauthorized('Token no es de tipo access');
                }

                if (isset($payload->jti) && $this->tokenService->isTokenRevoked($payload->jti)) {
                    $this->unauthorized('Token revocado', ['jti' => $payload->jti]);
                }
            } catch (ExpiredException $e) {
                $this->logger->warning("[AuthMiddelware] Access token expirado, intentando refresh");
                $payload = $this->attemptRefreshOrFail();
            } catch (\Exception $e) {
                $this->unauthorized('Token inválido (excepción decode)', ['error' => $e->getMessage()]);
            }
        }

        if (!isset($payload->exp)) {
            $this->unauthorized('Token sin expiración');
        }

        $timeLeft = $payload->exp - time();
        $this->logger->info("[AuthMiddelware] Tiempo restante del access", ['timeLeft' => $timeLeft]);

        $userData = $payload->data ?? null;
        if (!$userData) {
            $this->unauthorized('Token sin data de usuario');
        }

        if ($roles && !in_array($userData->role, $roles, true)) {
            $this->forbidden('Acceso prohibido para tu rol', ['role' => $userData->role]);
        }

        return $userData;
    }

    private function attemptRefreshOrFail(): \stdClass
    {
        $refreshJwt = $_COOKIE['refresh_token'] ?? null;
        if (!$refreshJwt) {
            $this->unauthorized('Acceso expirado y sin refresh token');
        }

        $tokens = $this->tokenService->refreshTokens($refreshJwt, $this->accessTTL, $this->refreshTTL);
        if (!$tokens) {
            $this->unauthorized('Refresh inválido o reutilizado. Re-login requerido.');
        }

        $this->setAuthCookies($tokens['access'], $tokens['refresh']);
        $decoded = $this->tokenService->decodeToken($tokens['access']);

        if (!$decoded) {
            $this->unauthorized('Error al decodificar nuevo access token');
        }

        $this->logger->info("[AuthMiddelware] Refresh exitoso, nuevos tokens generados");

        return $decoded;
    }

    private function setAuthCookies(string $accessJwt, string $refreshJwt): void
    {
        $cookieSecure = getenv('APP_ENV') === 'production';
        $cookieOptions = [
            'path' => '/',
            'httponly' => true,
            'secure' => $cookieSecure,
            'samesite' => 'Lax',
        ];

        setcookie('access_token', $accessJwt, array_merge($cookieOptions, ['expires' => time() + $this->accessTTL]));
        setcookie('refresh_token', $refreshJwt, array_merge($cookieOptions, ['expires' => time() + $this->refreshTTL]));

        $this->logger->info("[AuthMiddelware] Nuevas cookies seteadas");
    }

    private function unauthorized(string $message, array $context = []): void
    {
        $this->logger->error("[AuthMiddelware] Unauthorized: {$message}", $context);
        http_response_code(401);
        echo json_encode(['error' => $message]);
        exit;
    }

    private function forbidden(string $message, array $context = []): void
    {
        $this->logger->warning("[AuthMiddelware] Forbidden: {$message}", $context);
        http_response_code(403);
        echo json_encode(['error' => $message]);
        exit;
    }
}
