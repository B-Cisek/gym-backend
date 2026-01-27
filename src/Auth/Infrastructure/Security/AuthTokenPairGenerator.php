<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\Auth\Application\Service\AuthTokenPair;
use App\Auth\Application\Service\AuthTokenPairGeneratorInterface;
use App\Auth\Domain\UserNotFoundException;
use App\Auth\Infrastructure\Doctrine\Repository\UserRepository;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

readonly class AuthTokenPairGenerator implements AuthTokenPairGeneratorInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private RefreshTokenManagerInterface $refreshTokenManager,
        private UserRepository $userRepository,
        private int $refreshTokenTtl
    ) {}

    public function generateFor(string $userId): AuthTokenPair
    {
        $user = $this->userRepository->get($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $token = $this->jwtManager->create($user);
        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl(
            $user,
            $this->refreshTokenTtl
        );

        $this->refreshTokenManager->save($refreshToken);

        return new AuthTokenPair(
            token: $token,
            refreshToken: $refreshToken->getRefreshToken()
        );
    }
}
