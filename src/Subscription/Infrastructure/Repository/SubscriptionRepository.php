<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Repository;

use App\Shared\Domain\Id;
use App\Subscription\Domain\Subscription as DomainSubscription;
use App\Subscription\Domain\SubscriptionRepository as DomainSubscriptionRepository;
use App\Subscription\Infrastructure\Doctrine\Entity\Subscription;
use App\Subscription\Infrastructure\Doctrine\Repository\SubscriptionRepository as DoctrineSubscriptionRepository;

final readonly class SubscriptionRepository implements DomainSubscriptionRepository
{
    public function __construct(
        private DoctrineSubscriptionRepository $doctrineRepository,
    ) {}

    public function findByOwnerId(Id $ownerId): ?DomainSubscription
    {
        $entity = $this->doctrineRepository->findByOwnerId($ownerId->toString());

        return $entity !== null ? $this->toDomain($entity) : null;
    }

    private function toDomain(Subscription $entity): DomainSubscription
    {
        return DomainSubscription::restore(
            id: new Id($entity->getId()->toString()),
            ownerId: new Id($entity->getOwner()->getId()->toString()),
            status: $entity->getStatus(),
            startTime: $entity->getStartTime(),
            endTime: $entity->getEndTime(),
            cancelTime: $entity->getCancelTime(),
        );
    }
}
