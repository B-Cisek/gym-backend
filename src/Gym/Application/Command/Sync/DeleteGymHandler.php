<?php

declare(strict_types=1);

namespace App\Gym\Application\Command\Sync;

use App\Gym\Domain\GymAccessDeniedException;
use App\Gym\Domain\GymNotFoundException;
use App\Gym\Domain\GymRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Domain\Id;

final readonly class DeleteGymHandler implements CommandHandler
{
    public function __construct(
        private GymRepository $repository,
    ) {}

    /**
     * @throws GymNotFoundException
     * @throws GymAccessDeniedException
     */
    public function __invoke(DeleteGym $command): void
    {
        $gymId = new Id($command->gymId);
        $gym = $this->repository->get($gymId);

        if (!$gym->ownerId->equals(new Id($command->ownerId))) {
            throw new GymAccessDeniedException();
        }

        $this->repository->delete($gymId);
    }
}
