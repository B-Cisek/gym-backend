<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Owner\Domain\OwnerRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Domain\Id;
use App\Subscription\Application\Service\StripeGatewayInterface;
use App\Subscription\Domain\PlanPriceRepository;
use App\Subscription\Domain\SubscriptionAlreadyExistsException;
use App\Subscription\Domain\SubscriptionRepository;

final readonly class CreateCheckoutSessionHandler implements CommandHandler
{
    public function __construct(
        private OwnerRepository $ownerRepository,
        private PlanPriceRepository $planPriceRepository,
        private SubscriptionRepository $subscriptionRepository,
        private StripeGatewayInterface $stripeGateway,
    ) {}

    public function __invoke(CreateCheckoutSession $command): string
    {
        $owner = $this->ownerRepository->get(new Id($command->ownerId));

        $existingSubscription = $this->subscriptionRepository->findByOwnerId(new Id($command->ownerId));

        if ($existingSubscription !== null && $existingSubscription->isActive()) {
            throw new SubscriptionAlreadyExistsException();
        }

        $planPrice = $this->planPriceRepository->get(new Id($command->planPriceId));

        $stripeCustomerId = $owner->stripeCustomerId;

        if ($stripeCustomerId === null) {
            $stripeCustomerId = $this->stripeGateway->createCustomer(
                $owner->email ?? '',
                $owner->id->toString(),
            );
            $owner = $owner->withStripeCustomerId($stripeCustomerId);
            $this->ownerRepository->save($owner);
        }

        return $this->stripeGateway->createCheckoutSession($stripeCustomerId, $planPrice->getStripePriceId(), $owner->id->toString());
    }
}
