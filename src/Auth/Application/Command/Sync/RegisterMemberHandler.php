<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\Sync;

use App\Shared\Application\Command\Sync\CommandHandler;

final readonly class RegisterMemberHandler implements CommandHandler
{
    public function __construct(
        private RegisterUserHandler $registerUserHandler,
    )
    {
    }

    public function __invoke(RegisterMember $command): void
    {
        ($this->registerUserHandler)($command->toRegisterUser());
    }
}
