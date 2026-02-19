<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Repository;

use App\Subscription\Domain\PlanPriceNotFoundException;
use App\Subscription\Infrastructure\Doctrine\Entity\PlanPrice;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PlanPriceRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function get(string $id): PlanPrice
    {
        $qb = $this->entityManager->createQueryBuilder();

        $result = $qb->select('pp')
            ->from(PlanPrice::class, 'pp')
            ->where('pp.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($result === null) {
            throw new PlanPriceNotFoundException();
        }

        return $result;
    }

    public function findByStripeId(string $stripeId): ?PlanPrice
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('pp')
            ->from(PlanPrice::class, 'pp')
            ->where('pp.stripePriceId = :stripeId')
            ->setParameter('stripeId', $stripeId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
