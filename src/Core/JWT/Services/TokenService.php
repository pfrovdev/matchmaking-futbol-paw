<?php

namespace Paw\Core\JWT\Services;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Paw\Core\JWT\TokenStorageInterface;
use Firebase\JWT\ExpiredException;

class TokenService
{
    private string $secret;
    private string $algorithm;
    private string $issuer;
    private TokenStorageInterface $storage;
    private bool $useHashing;

    public function __construct(TokenStorageInterface $storage, bool $useHashing = true)
    {
        $this->secret = getenv('JWT_SECRET') ?: throw new Exception('JWT_SECRET not configured');
        $this->algorithm = getenv('JWT_ALG') ?: 'HS256';
        $this->issuer = getenv('JWT_APP_URL') ?: '';
        $this->storage = $storage;
        $this->useHashing = $useHashing;
        \Firebase\JWT\JWT::$leeway = (int)(getenv('JWT_LEEWAY') ?: 60);
    }

    private function now(): int
    {
        return time();
    }

    public function createToken(array $claims, int $ttl, string $type = 'access'): string
    {
        $now = $this->now();
        $jti = bin2hex(random_bytes(16));
        $payload = [
            'iss' => $this->issuer,
            'iat' => $now,
            'exp' => $now + $ttl,
            'jti' => $jti,
            'typ' => $type, // puede ser 'access' o 'refresh'
            'data' => (object) $claims,
        ];

        $jwt = JWT::encode($payload, $this->secret, $this->algorithm);

        // Si es refresh, se guarda en storage para poder validar y revocar
        if ($type === 'refresh') {
            $jtiHash = $this->hashJti($jti);
            $this->storage->saveRefresh($jtiHash, (int)$claims['id_equipo'], $payload['exp']);
        }

        return $jwt;
    }

    public function createAccessToken(array $claims, int $ttl): string
    {
        return $this->createToken($claims, $ttl, 'access');
    }

    public function createRefreshToken(array $claims, int $ttl): string
    {
        return $this->createToken($claims, $ttl, 'refresh');
    }

    public function decodeToken(string $token): ?\stdClass
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            // verifico que no haya sido revocado
            if (isset($decoded->jti) && $this->isTokenRevoked($decoded->jti)) {
                return null;
            }
            return $decoded;
        } catch (ExpiredException $e) {
            throw $e;
        } catch (Exception $e) {
            return null;
        }
    }

    public function revokeToken(string $jti, int $expiresAt): void
    {
        $this->storage->revoke($jti, $expiresAt);
    }

    public function isTokenRevoked(string $jti): bool
    {
        return $this->storage->isRevoked($jti);
    }

    private function hashJti(string $jti): string
    {
        if ($this->useHashing) {
            return hash('sha256', $jti . $this->secret);
        }
        return $jti;
    }

    // recibo el refresh jwt, lo valido, creo los nuevos tokens y roto el refresh
    public function refreshTokens(string $refreshJwt, int $accessTtl, int $refreshTtl): ?array
    {
        $decoded = $this->decodeToken($refreshJwt);
        if (!$decoded) return null;

        // Tiene que ser si o si un refresh token
        if (!isset($decoded->typ) || $decoded->typ !== 'refresh') return null;

        // verificar que el jti exista en storage (no revocado/rotado) y obtengo el duseño
        $jti = $decoded->jti ?? null;
        if (!$jti) return null;
        $jtiHash = $this->hashJti($jti);

        $ownerId = $this->storage->getRefreshOwner($jtiHash);
        if ($ownerId === null) {
            // si no existe o fue revocado/rotado
            return null;
        }

        // Verifico que el ownerId coincida con el equipo del token
        $claims = (array)($decoded->data ?? []);
        if (!isset($claims['id_equipo']) || (int)$claims['id_equipo'] !== (int)$ownerId) {
            return null;
        }

        $access = $this->createAccessToken($claims, $accessTtl);
        $newRefresh = $this->createRefreshToken($claims, $refreshTtl);

        // Roto el refresh token actual (lo revoco y lo borro del storage)
        $this->revokeToken($jti, $decoded->exp ?? ($this->now()));
        $this->storage->deleteRefresh($jtiHash);

        return ['access' => $access, 'refresh' => $newRefresh];
    }
}
