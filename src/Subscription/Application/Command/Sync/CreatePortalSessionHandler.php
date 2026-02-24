<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Owner\Domain\OwnerRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Domain\Id;
use App\Subscription\Application\Service\StripeGatewayInterface;
use App\Subscription\Domain\NoStripeCustomerException;

final readonly class CreatePortalSessionHandler implements CommandHandler
{
    public function __construct(
        private OwnerRepository $ownerRepository,
        private StripeGatewayInterface $stripeGateway,
    ) {}

    public function __invoke(CreatePortalSession $command): string
    {
        $owner = $this->ownerRepository->get(new Id($command->ownerId));

        if ($owner->stripeCustomerId === null) {
            throw new NoStripeCustomerException();
        }

        return $this->stripeGateway->createPortalSession($owner->stripeCustomerId);
    }
}
