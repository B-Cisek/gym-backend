<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Repository;

use App\Subscription\Domain\PlanTier;
use App\Subscription\Infrastructure\Doctrine\Entity\Plan;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PlanRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findByTier(PlanTier $tier): ?Plan
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('p')
            ->from(Plan::class, 'p')
            ->where('p.tier = :tier')
            ->setParameter('tier', $tier)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /** @param Plan[] $entities */
    public function saveMany(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }
}
