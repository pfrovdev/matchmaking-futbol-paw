<?php

namespace Paw\Core\JWT;

interface TokenStorageInterface
{

    public function revoke(string $jti, int $expiresAt): void;

    public function isRevoked(string $jti): bool;

    public function saveRefresh(string $jtiHash, int $userId, int $expiresAt): void;

    public function getRefreshOwner(string $jtiHash): ?int;

    public function deleteRefresh(string $jtiHash): void;
    
}
