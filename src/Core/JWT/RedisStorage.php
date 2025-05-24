<?php
namespace Paw\Core\JWT;

use Predis\Client;
use Paw\Core\JWT\TokenStorageInterface;
use Exception;

class RedisStorage implements TokenStorageInterface
{
    private Client $client;
    private string $prefix;

    public function __construct(
        string $host   = '127.0.0.1',
        int    $port   = 6379,
        string $prefix = 'jwt:blacklist:'
    ) {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host'   => $host,
            'port'   => $port,
        ]);
        $this->prefix = $prefix;
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
}