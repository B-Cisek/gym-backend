<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\Id;

final readonly class User
{
    /**
     * @param array<UserRole> $roles
     */
    private function __construct(
        public Id $id,
        public Email $email,
        public array $roles,
    ) {}

    /**
     * @param array<UserRole> $roles
     */
    public static function register(Id $id, Email $email, array $roles): self
    {
        return new self($id, $email, $roles);
    }

    /**
     * @param array<UserRole> $roles
     */
    public static function restore(Id $id, Email $email, array $roles): self
    {
        return new self($id, $email, $roles);
    }
}
