<?php

namespace Paw\Core\JWT;

use Predis\Client;
use Exception;

class RedisStorage implements TokenStorageInterface
{
    private Client $client;
    private string $prefix; // para blacklist
    private string $refreshPrefix; // para refresh tokens

    public function __construct(
        string $host = '127.0.0.1',
        int    $port = 6379,
        string $prefix = 'jwt:blacklist:',
        string $refreshPrefix = 'jwt:refresh:'
    ) {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => $host,
            'port' => $port,
        ]);
        $this->prefix = $prefix;
        $this->refreshPrefix = $refreshPrefix;
        try {
            $this->client->connect();
        } catch (Exception $e) {
            throw new Exception("No se pudo conectar a Redis: " . $e->getMessage());
        }
    }

    public function revoke(string $jti, int $expiresAt): void
    {
        $key = $this->prefix . $jti;
        $ttl = max(1, $expiresAt - time());
        $this->client->setex($key, $ttl, 'revoked');
    }

    public function isRevoked(string $jti): bool
    {
        $key = $this->prefix . $jti;
        return (bool) $this->client->exists($key);
    }

    public function saveRefresh(string $jtiHash, int $userId, int $expiresAt): void
    {
        $key = $this->refreshPrefix . $jtiHash;
        $ttl = max(1, $expiresAt - time());
        $this->client->setex($key, $ttl, (string)$userId); // para poder verificar el usuario dueño del token
    }

    public function getRefreshOwner(string $jtiHash): ?int
    {
        $key = $this->refreshPrefix . $jtiHash;
        $val = $this->client->get($key);
        return $val === null ? null : (int)$val;
    }

    public function deleteRefresh(string $jtiHash): void
    {
        $key = $this->refreshPrefix . $jtiHash;
        $this->client->del([$key]);
    }
}
