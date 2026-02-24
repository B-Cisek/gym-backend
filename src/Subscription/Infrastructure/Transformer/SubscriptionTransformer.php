<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Transformer;

use App\Owner\Infrastructure\Doctrine\Entity\Owner;
use App\Shared\Domain\Id;
use App\Subscription\Domain\Subscription as DomainSubscription;
use App\Subscription\Infrastructure\Doctrine\Entity\PlanPrice;
use App\Subscription\Infrastructure\Doctrine\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class SubscriptionTransformer
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function fromDomain(DomainSubscription $subscription): Subscription
    {
        /** @var Owner $owner */
        $owner = $this->entityManager->getReference(
            Owner::class,
            Uuid::fromString($subscription->ownerId->toString()),
        );

        /** @var PlanPrice $price */
        $price = $this->entityManager->getReference(
            PlanPrice::class,
            Uuid::fromString($subscription->planPriceId->toString()),
        );

        return new Subscription(
            id: Uuid::fromString($subscription->id->toString()),
            stripeSubscriptionId: $subscription->stripeSubscriptionId,
            owner: $owner,
            price: $price,
            nextPrice: null,
            status: $subscription->status,
            startTime: $subscription->startTime,
            endTime: $subscription->endTime,
        );
    }

    public function toDomain(Subscription $subscription): DomainSubscription
    {
        return DomainSubscription::restore(
            id: new Id($subscription->getId()->toString()),
            ownerId: new Id($subscription->getOwner()->getId()->toString()),
            planPriceId: new Id($subscription->getPrice()->getId()->toString()),
            status: $subscription->getStatus(),
            startTime: $subscription->getStartTime(),
            endTime: $subscription->getEndTime(),
            stripeSubscriptionId: $subscription->getStripeSubscriptionId(),
            cancelTime: $subscription->getCancelTime(),
        );
    }
}
