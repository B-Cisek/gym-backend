<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Security;

use App\Auth\Domain\TokenGeneratorInterface;
use App\Auth\Infrastructure\Doctrine\Entity\User;
use App\Shared\Domain\Id;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

readonly class TokenGenerator implements TokenGeneratorInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private EntityManagerInterface $entityManager,
    ) {}

    public function generateFor(Id $id): string
    {
        $user = $this->entityManager->getRepository(User::class)->find($id->toString());

        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        return $this->jwtManager->create($user);
    }
}
