<?php

declare(strict_types=1);

namespace App\Owner\Application\Command\Sync;

use App\Owner\Domain\OwnerRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Domain\Id;

final readonly class CompleteSetupHandler implements CommandHandler
{
    public function __construct(private OwnerRepository $repository) {}

    public function __invoke(CompleteSetup $command): void
    {
        $owner = $this->repository->get(new Id($command->ownerId));

        $owner->setProfileCompleted();

        $this->repository->save($owner);
    }
}
