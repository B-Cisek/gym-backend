<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\Sync;

use App\Auth\Domain\UserRole;
use App\Shared\Application\Command\Sync\Command;

final readonly class RegisterMember implements Command
{
    public function __construct(
        public string $email,
        public string $password,
    )
    {
    }

    public function toRegisterUser(): RegisterUser
    {
        return new RegisterUser(
            email: $this->email,
            password: $this->password,
            roles: [UserRole::MEMBER]
        );
    }
}
