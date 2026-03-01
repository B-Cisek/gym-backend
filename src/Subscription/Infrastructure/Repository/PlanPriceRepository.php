<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Repository;

use App\Shared\Domain\Id;
use App\Subscription\Domain\PlanPrice;
use App\Subscription\Domain\PlanPriceNotFoundException;
use App\Subscription\Domain\PlanPriceRepository as DomainPlanPriceRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PlanPriceRepository implements DomainPlanPriceRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function get(Id $id): PlanPrice
    {
        $entity = $this->findEntity($id);

        if ($entity === null) {
            throw new PlanPriceNotFoundException();
        }

        return $entity;
    }

    public function findByStripeId(string $stripeId): ?PlanPrice
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('pp')
            ->from(PlanPrice::class, 'pp')
            ->where('pp.stripePriceId = :stripeId')
            ->setParameter('stripeId', $stripeId)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity !== null ? $entity : null;
    }

    private function findEntity(Id $id): ?PlanPrice
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('pp')
            ->from(PlanPrice::class, 'pp')
            ->where('pp.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
