<?php
namespace Paw\Core\JWT;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class Auth
{

    private static string $key;
    private static string $alg;

    private static function init(): void
    {
        if (!isset(self::$key)) {
            $secret = getenv('JWT_SECRET');
            if (! $secret) {
                throw new Exception('JWT_SECRET not configured');
            }
            self::$key = $secret;
        }

        if (!isset(self::$alg)) {
            $algorithm = getenv('JWT_ALG') ?: 'HS256';
            self::$alg = $algorithm;
        }
    }

    public static function generarToken(array $data): string
    {
        self::init();
        $expSeconds = (int) getenv('JWT_EXP_SEGUNDOS');
        $payload = [
            'iss'  => getenv('JWT_APP_URL') ?: '',
            'iat'  => time(),
            'exp'  => time() + $expSeconds,
            'data' => $data,
        ];
        return JWT::encode($payload, self::$key, self::$alg);
    }

    public static function validarToken(string $jwt)
    {
        self::init();
        try {
            return JWT::decode($jwt, new Key(self::$key, self::$alg));
        } catch (Exception $e) {
            return null;
        }
    }
}