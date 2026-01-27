<?php

declare(strict_types=1);

namespace App\Auth\Application\Service;

final readonly class AuthTokenPair
{
    public function __construct(
        public string $token,
        public string $refreshToken
    ) {}
}
