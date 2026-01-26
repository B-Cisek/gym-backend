<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Utils;

use App\Auth\Domain\PasswordHasherInterface;
use App\Auth\Infrastructure\Doctrine\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface as SymfonyPasswordHasher;

readonly class PasswordHasher implements PasswordHasherInterface
{
    public function __construct(private SymfonyPasswordHasher $hasher) {}

    public function hash(string $password): string
    {
        $hasher = $this->hasher->getPasswordHasher(User::class);

        return $hasher->hash($password);
    }

    public function verify(string $password, string $hashedPassword): bool
    {
        $hasher = $this->hasher->getPasswordHasher(User::class);

        return $hasher->verify($hashedPassword, $password);
    }
}
