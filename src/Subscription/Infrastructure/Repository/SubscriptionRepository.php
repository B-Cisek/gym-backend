<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Repository;

use App\Shared\Domain\Id;
use App\Subscription\Domain\Subscription;
use App\Subscription\Domain\SubscriptionNotFoundException;
use App\Subscription\Domain\SubscriptionRepository as DomainSubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SubscriptionRepository implements DomainSubscriptionRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findByOwnerId(Id $ownerId): ?Subscription
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('s')
            ->from(Subscription::class, 's')
            ->join('s.owner', 'o')
            ->where('o.id = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByStripeSubscriptionId(string $stripeSubscriptionId): ?Subscription
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('s')
            ->from(Subscription::class, 's')
            ->where('s.stripeSubscriptionId = :stripeId')
            ->setParameter('stripeId', $stripeSubscriptionId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function update(Subscription $subscription): void
    {
        $existing = $this->findByStripeSubscriptionId($subscription->stripeSubscriptionId);

        if ($existing === null) {
            throw new SubscriptionNotFoundException();
        }

        $this->save($subscription);
    }

    public function save(Subscription $subscription): void
    {
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }
}
