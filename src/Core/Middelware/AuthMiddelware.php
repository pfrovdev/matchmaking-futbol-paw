<?php
namespace Paw\Core\Middelware;

use Paw\Core\JWT\JsonFileStorage;
use Paw\Core\JWT\Services\TokenService;

class AuthMiddelware
{
    private TokenService $tokenService;
    private int $accessTTL;
    private int $refreshTTL;
    private int $refreshWindow;

    public function __construct()
    {
        $storage = new JsonFileStorage(__DIR__ . '/../blacklist.json');
        $this->tokenService   = new TokenService($storage);
        $this->accessTTL      = (int) getenv('JWT_ACCESS_TTL');
        $this->refreshTTL     = (int) getenv('JWT_REFRESH_TTL');
        $this->refreshWindow  = (int) getenv('JWT_REFRESH_WINDOW');
    }

    public function verificar(array $roles = []): object
    {
        $accessToken = $_COOKIE['access_token'] ?? null;
        if (!$accessToken) {
            $this->unauthorized('No autenticado');
        }

        $payload = $this->tokenService->decodeToken($accessToken);
        if (!$payload || $this->tokenService->isTokenRevoked($payload->jti)) {
            $this->unauthorized('Token inválido o expirado');
        }

        // Auto-refresh logic
        $timeLeft = $payload->exp - time();
        if ($timeLeft < $this->refreshWindow) {
            $this->refreshTokens((array) $payload->data);
        }

        $userData = $payload->data;
        if ($roles && !in_array($userData->role, $roles, true)) {
            $this->forbidden('Acceso prohibido para tu rol');
        }

        return $userData;
    }

    private function refreshTokens(array $data): void
    {
        // Revoke old refresh
        $oldRefresh = $_COOKIE['refresh_token'] ?? null;
        if ($oldRefresh) {
            $oldPayload = $this->tokenService->decodeToken($oldRefresh);
            if ($oldPayload) {
                $this->tokenService->revokeToken($oldPayload->jti, $oldPayload->exp);
            }
        }
        // Create new tokens
        $newAccess  = $this->tokenService->createToken($data, $this->accessTTL);
        $newRefresh = $this->tokenService->createToken($data, $this->refreshTTL);

        setcookie('access_token', $newAccess, [
            'expires'=>time()+$this->accessTTL,
            'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Strict',
        ]);
        setcookie('refresh_token', $newRefresh, [
            'expires'=>time()+$this->refreshTTL,
            'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Strict',
        ]);
    }

    private function unauthorized(string $message): void
    {
        http_response_code(401);
        echo json_encode(['error' => $message]);
        exit;
    }

    private function forbidden(string $message): void
    {
        http_response_code(403);
        echo json_encode(['error' => $message]);
        exit;
    }
}