<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final readonly class AuthContext
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private JWTTokenManagerInterface $jwtTokenManager,
    ) {}

    public function getOwnerId(): string
    {
        return $this->payload()['owner_id'];
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        return $this->jwtTokenManager->decode($token);
    }
}
