<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Repository;

use App\Shared\Domain\Id;
use App\Subscription\Domain\Subscription as DomainSubscription;
use App\Subscription\Domain\SubscriptionNotFoundException;
use App\Subscription\Domain\SubscriptionRepository as DomainSubscriptionRepository;
use App\Subscription\Infrastructure\Doctrine\Repository\SubscriptionRepository as DoctrineSubscriptionRepository;
use App\Subscription\Infrastructure\Transformer\SubscriptionTransformer;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SubscriptionRepository implements DomainSubscriptionRepository
{
    public function __construct(
        private DoctrineSubscriptionRepository $doctrineRepository,
        private EntityManagerInterface $entityManager,
        private SubscriptionTransformer $transformer
    ) {}

    public function findByOwnerId(Id $ownerId): ?DomainSubscription
    {
        $entity = $this->doctrineRepository->findByOwnerId($ownerId->toString());

        return $entity !== null ? $this->transformer->toDomain($entity) : null;
    }

    public function findByStripeSubscriptionId(string $stripeSubscriptionId): ?DomainSubscription
    {
        $entity = $this->doctrineRepository->findByStripeId($stripeSubscriptionId);

        return $entity !== null ? $this->transformer->toDomain($entity) : null;
    }

    public function update(DomainSubscription $subscription): void
    {
        $entity = $this->doctrineRepository->findByStripeId($subscription->stripeSubscriptionId);

        if ($entity === null) {
            throw new SubscriptionNotFoundException();
        }

        $entity
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setStatus($subscription->status)
            ->setCancelTime($subscription->cancelTime)
            ->setStartTime($subscription->startTime)
            ->setEndTime($subscription->endTime)
        ;

        $this->entityManager->flush();
    }

    public function save(DomainSubscription $subscription): void
    {
        $this->doctrineRepository->save($this->transformer->fromDomain($subscription));
    }
}
