<?php

declare(strict_types=1);

namespace App\Owner\Application\Command\Sync;

use App\Owner\Domain\OwnerNotFoundException;
use App\Owner\Domain\OwnerRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Domain\Address;
use App\Shared\Domain\Id;

final readonly class UpdateOwnerHandler implements CommandHandler
{
    public function __construct(
        private OwnerRepository $repository,
    ) {}

    /**
     * @throws OwnerNotFoundException
     */
    public function __invoke(UpdateOwner $command): void
    {
        $owner = $this->repository->getByUserId(new Id($command->userId));

        $updated = $owner->update(
            companyName: $command->companyName,
            taxId: $command->taxId,
            phone: $command->phone,
            address: new Address(
                street: $command->street,
                city: $command->city,
                postalCode: $command->postalCode,
            )
        );

        $this->repository->save($updated);
    }
}
