<?php

declare(strict_types=1);

namespace App\Owner\Infrastructure\EventListener;

use App\Auth\Domain\UserRegistered;
use App\Auth\Domain\UserRepository;
use App\Owner\Application\Command\Sync\CreateOwner;
use App\Shared\Application\Command\Sync\CommandBus;

final readonly class CreateOwnerOnUserRegisteredListener
{
    public function __construct(
        private UserRepository $userRepository,
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UserRegistered $event): void
    {
        $user = $this->userRepository->get($event->userId);

        if (!$user->isOwner()) {
            return;
        }

        $this->commandBus->dispatch(new CreateOwner(userId: $event->userId->toString()));
    }
}
