<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Owner\Domain\OwnerNotFoundException;
use App\Owner\Infrastructure\Doctrine\Repository\OwnerRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Subscription\Domain\NoStripeCustomerException;
use App\Subscription\Infrastructure\Stripe\StripeGateway;

final readonly class CreatePortalSessionHandler implements CommandHandler
{
    public function __construct(
        private OwnerRepository $ownerRepository,
        private StripeGateway $stripeGateway,
    ) {}

    public function __invoke(CreatePortalSession $command): string
    {
        $owner = $this->ownerRepository->get($command->ownerId);

        if ($owner === null) {
            throw new OwnerNotFoundException();
        }

        $stripeCustomerId = $owner->getStripeCustomerId();

        if ($stripeCustomerId === null) {
            throw new NoStripeCustomerException();
        }

        return $this->stripeGateway->createPortalSession($stripeCustomerId);
    }
}
