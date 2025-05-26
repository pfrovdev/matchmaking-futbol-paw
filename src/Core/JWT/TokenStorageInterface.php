<?php

namespace Paw\Core\JWT;

interface TokenStorageInterface
{
    public function revoke(string $jti, int $expiresAt): void;
    public function isRevoked(string $jti): bool;
}
