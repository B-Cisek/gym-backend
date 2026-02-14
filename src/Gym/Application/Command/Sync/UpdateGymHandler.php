<?php

declare(strict_types=1);

namespace App\Gym\Application\Command\Sync;

use App\Gym\Domain\GymAccessDeniedException;
use App\Gym\Domain\GymNotFoundException;
use App\Gym\Domain\GymRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Domain\Address;
use App\Shared\Domain\Id;

final readonly class UpdateGymHandler implements CommandHandler
{
    public function __construct(
        private GymRepository $repository,
    ) {}

    /**
     * @throws GymNotFoundException
     * @throws GymAccessDeniedException
     */
    public function __invoke(UpdateGym $command): void
    {
        $gym = $this->repository->get(new Id($command->gymId));

        if (!$gym->ownerId->equals(new Id($command->ownerId))) {
            throw new GymAccessDeniedException();
        }

        $updated = $gym->update(
            name: $command->name,
            address: new Address(
                street: $command->street,
                city: $command->city,
                postalCode: $command->postalCode,
            ),
        );

        $this->repository->save($updated);
    }
}
