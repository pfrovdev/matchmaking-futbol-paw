<?php

namespace Paw\Core\JWT\Services;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Paw\Core\JWT\TokenStorageInterface;

class TokenService
{
    private string $secret;
    private string $algorithm;
    private string $issuer;
    private TokenStorageInterface $storage;

    public function __construct(TokenStorageInterface $storage)
    {
        $this->secret    = getenv('JWT_SECRET') ?: throw new Exception('JWT_SECRET not configured');
        $this->algorithm = getenv('JWT_ALG') ?: 'HS256';
        $this->issuer    = getenv('JWT_APP_URL') ?: '';
        $this->storage   = $storage;
    }

    public function createToken(array $claims, int $ttl): string
    {
        $now = time();
        $payload = [
            'iss' => $this->issuer,
            'iat' => $now,
            'exp' => $now + $ttl,
            'jti' => bin2hex(random_bytes(16)),
            'data' => (object) $claims,
        ];
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function decodeToken(string $token): ?\stdClass
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (Exception) {
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
}
