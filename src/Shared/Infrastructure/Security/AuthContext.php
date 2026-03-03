<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class AuthContext
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private JWTTokenManagerInterface $jwtTokenManager,
    ) {}

    public function getOwnerId(): string
    {
        $ownerId = $this->payload()['owner_id'] ?? null;

        if (!is_string($ownerId)) {
            throw new AccessDeniedException('Owner context is required.');
        }

        return $ownerId;
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TokenInterface) {
            throw new AccessDeniedException('Authentication token was not found.');
        }

        $payload = $this->jwtTokenManager->decode($token);

        if ($payload === false) {
            throw new AccessDeniedException('Authentication token payload is invalid.');
        }

        return $payload;
    }
}
