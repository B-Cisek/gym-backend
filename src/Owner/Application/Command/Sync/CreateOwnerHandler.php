<?php

declare(strict_types=1);

namespace App\Owner\Application\Command\Sync;

use App\Owner\Domain\Owner;
use App\Owner\Domain\OwnerAlreadyExistsException;
use App\Owner\Domain\OwnerRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Application\Service\IdGeneratorInterface;
use App\Shared\Domain\Id;

final readonly class CreateOwnerHandler implements CommandHandler
{
    public function __construct(
        private OwnerRepository $repository,
        private IdGeneratorInterface $idGenerator,
    ) {}

    /**
     * @throws OwnerAlreadyExistsException
     */
    public function __invoke(CreateOwner $command): void
    {
        $userId = new Id($command->userId);

        if ($this->repository->existsByUserId($userId)) {
            throw new OwnerAlreadyExistsException();
        }

        $owner = Owner::create(
            id: $this->idGenerator->generate(),
            userId: $userId,
        );

        $this->repository->save($owner);
    }
}
