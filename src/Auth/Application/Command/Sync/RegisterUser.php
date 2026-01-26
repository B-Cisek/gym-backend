<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\Sync;

use App\Auth\Domain\UserRole;
use App\Shared\Application\Command\Sync\Command;

final readonly class RegisterUser implements Command
{
    /**
     * @param array<UserRole> $roles
     */
    public function __construct(
        public string $email,
        public string $password,
        public array $roles,
    ) {}
}
