<?php

declare(strict_types=1);

namespace App\Owner\Application\Command\Sync;

use App\Auth\Domain\UserRepository;
use App\Owner\Domain\Owner;
use App\Owner\Domain\OwnerAlreadyExistsException;
use App\Owner\Domain\OwnerRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Application\Service\IdGeneratorInterface;
use App\Shared\Domain\Id;
use App\Subscription\Application\Service\StripeGatewayInterface;
use Psr\Log\LoggerInterface;

final readonly class CreateOwnerHandler implements CommandHandler
{
    public function __construct(
        private OwnerRepository $repository,
        private UserRepository $userRepository,
        private IdGeneratorInterface $idGenerator,
        private StripeGatewayInterface $stripeGateway,
        private LoggerInterface $stripeLogger,
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

        $user = $this->userRepository->get($userId);
        $ownerId = $this->idGenerator->generate();

        $owner = Owner::create(
            id: $ownerId,
            userId: $userId,
            stripeCustomerId: $this->getCustomerId($user->email->value, $ownerId->toString())
        );

        $this->repository->save($owner);
    }

    private function getCustomerId(string $email, string $ownerId): ?string
    {
        try {
            return $this->stripeGateway->createCustomer(email: $email, ownerId: $ownerId);
        } catch (\Throwable $e) {
            $this->stripeLogger->error('FAILED_TO_CREATE_CUSTOMER', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return null;
        }
    }
}
