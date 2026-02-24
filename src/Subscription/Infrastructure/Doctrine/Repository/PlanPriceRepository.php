<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Repository;

use App\Shared\Domain\Id;
use App\Subscription\Domain\PlanPrice as DomainPlanPrice;
use App\Subscription\Domain\PlanPriceNotFoundException;
use App\Subscription\Domain\PlanPriceRepository as DomainPlanPriceRepository;
use App\Subscription\Infrastructure\Doctrine\Entity\PlanPrice;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PlanPriceRepository implements DomainPlanPriceRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function get(Id $id): DomainPlanPrice
    {
        $entity = $this->findEntity($id->toString());

        if ($entity === null) {
            throw new PlanPriceNotFoundException();
        }

        return $this->toDomain($entity);
    }

    public function findByStripeId(string $stripeId): ?DomainPlanPrice
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('pp')
            ->from(PlanPrice::class, 'pp')
            ->where('pp.stripePriceId = :stripeId')
            ->setParameter('stripeId', $stripeId)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity !== null ? $this->toDomain($entity) : null;
    }

    private function findEntity(string $id): ?PlanPrice
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

    private function toDomain(PlanPrice $entity): DomainPlanPrice
    {
        return DomainPlanPrice::restore(
            id: new Id($entity->getId()->toString()),
            stripeId: $entity->getStripePriceId(),
            intervalValue: $entity->getInterval()->getValue(),
            intervalUnit: $entity->getInterval()->getUnit(),
            price: $entity->getPrice()->getValue(),
            currency: $entity->getPrice()->getCurrency(),
            tier: $entity->getPlan()->getTier(),
            gymsLimit: $entity->getPlan()->getGymsLimit(),
            staffLimit: $entity->getPlan()->getStaffLimit(),
        );
    }
}
