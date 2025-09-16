<?php
namespace Paw\Core\JWT;
use Paw\Core\JWT\TokenStorageInterface;

class JsonFileStorage implements TokenStorageInterface
{
    private string $file;
    private array $cache = [];
    private array $refreshCache = [];

    public function __construct(string $filePath)
    {
        $this->file = $filePath;
        $dir = dirname($filePath);
        if (!is_dir($dir))
            mkdir($dir, 0775, true);

        if (file_exists($filePath)) {
            $this->cache = json_decode(file_get_contents($filePath), true) ?: [];
        }

        // Separar cache para refresh tokens
        $refreshFile = $filePath . '.refresh';
        if (file_exists($refreshFile)) {
            $this->refreshCache = json_decode(file_get_contents($refreshFile), true) ?: [];
        }
    }

    private function persistRefresh(): void
    {
        file_put_contents($this->file . '.refresh', json_encode($this->refreshCache));
    }

    // Métodos existentes
    public function revoke(string $jti, int $expiresAt): void
    {
        $this->cache[$jti] = $expiresAt;
        file_put_contents($this->file, json_encode($this->cache));
    }

    public function isRevoked(string $jti): bool
    {
        if (!isset($this->cache[$jti]))
            return false;
        if (time() > $this->cache[$jti]) {
            unset($this->cache[$jti]);
            file_put_contents($this->file, json_encode($this->cache));
            return false;
        }
        return true;
    }

    // ===== Implementación de métodos faltantes =====
    public function saveRefresh(string $jtiHash, int $userId, int $expiresAt): void
    {
        $this->refreshCache[$jtiHash] = ['userId' => $userId, 'expiresAt' => $expiresAt];
        $this->persistRefresh();
    }

    public function getRefreshOwner(string $jtiHash): ?int
    {
        if (!isset($this->refreshCache[$jtiHash]))
            return null;
        if (time() > $this->refreshCache[$jtiHash]['expiresAt']) {
            unset($this->refreshCache[$jtiHash]);
            $this->persistRefresh();
            return null;
        }
        return $this->refreshCache[$jtiHash]['userId'];
    }

    public function deleteRefresh(string $jtiHash): void
    {
        unset($this->refreshCache[$jtiHash]);
        $this->persistRefresh();
    }
}