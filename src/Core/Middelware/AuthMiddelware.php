<?php
namespace Paw\Core\Middleware;

use Paw\Core\JWT\Auth;

class AuthMiddleware
{
    // Verifica que el token sea válido y no haya expirado
    public static function verificar(): object
    {
        $jwt = $_COOKIE['paw_token'] ?? null;
        if (! $jwt) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }

        $payload = Auth::validarToken($jwt);
        if (! $payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido o expirado']);
            exit;
        }

        return $payload->data; 
    }

    public static function verificarRoles(array $rolesPermitidos): object
    {
        $data = self::verificar();
        if (!in_array($data->role, $rolesPermitidos, true)) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso prohibido para tu rol']);
            exit;
        }
        return $data;
    }
}