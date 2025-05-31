<?php
namespace Paw\Core\JWT;
use Paw\Core\JWT\TokenStorageInterface;

class JsonFileStorage implements TokenStorageInterface
{
    private string $file;
    private array $cache = [];

    public function __construct(string $filePath)
    {
        $this->file = $filePath;
         $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (file_exists($filePath)) {
            $this->cache = json_decode(file_get_contents($filePath), true) ?: [];
        }
    }

    public function revoke(string $jti, int $expiresAt): void
    {
        $this->cache[$jti] = $expiresAt;
        file_put_contents($this->file, json_encode($this->cache));
    }

    public function isRevoked(string $jti): bool
    {
        if (!isset($this->cache[$jti])) {
            return false;
        }
        if (time() > $this->cache[$jti]) {
            unset($this->cache[$jti]);
            file_put_contents($this->file, json_encode($this->cache));
            return false;
        }
        return true;
    }
}