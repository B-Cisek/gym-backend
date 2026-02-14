<?php

declare(strict_types=1);

namespace App\Gym\Application\Command\Sync;

use App\Gym\Domain\Gym;
use App\Gym\Domain\GymRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Application\Service\IdGeneratorInterface;
use App\Shared\Domain\Address;
use App\Shared\Domain\Id;

final readonly class CreateGymHandler implements CommandHandler
{
    public function __construct(
        private GymRepository $repository,
        private IdGeneratorInterface $idGenerator,
    ) {}

    public function __invoke(CreateGym $command): void
    {
        $gym = Gym::create(
            id: $this->idGenerator->generate(),
            ownerId: new Id($command->ownerId),
            name: $command->name,
            address: new Address(
                street: $command->street,
                city: $command->city,
                postalCode: $command->postalCode,
            ),
        );

        $this->repository->save($gym);
    }
}
