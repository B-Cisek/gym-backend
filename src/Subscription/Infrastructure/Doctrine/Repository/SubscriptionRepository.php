<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Repository;

use App\Subscription\Infrastructure\Doctrine\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SubscriptionRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(Subscription $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findByOwnerId(string $ownerId): ?Subscription
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

    public function findByStripeId(string $stripeId): ?Subscription
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('s')
            ->from(Subscription::class, 's')
            ->where('s.stripeSubscriptionId = :stripeId')
            ->setParameter('stripeId', $stripeId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
