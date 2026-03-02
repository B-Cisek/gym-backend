<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Query;

use App\Subscription\Application\Query\GetPlans;
use App\Subscription\Application\Query\Result\PlanCollection;
use App\Subscription\Application\Query\Result\PlanPriceResult;
use App\Subscription\Application\Query\Result\PlanResult;
use App\Subscription\Domain\Plan;
use App\Subscription\Domain\PlanPrice;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetPlansQuery implements GetPlans
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(): PlanCollection
    {
        $qb = $this->entityManager->createQueryBuilder();

        $result = $qb->select('p', 'pp')
            ->from(Plan::class, 'p')
            ->leftJoin('p.prices', 'pp')
            ->where('p.isActive = true')
            ->orderBy('p.tier', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        /** @var array<PlanResult> $plans */
        $plans = [];

        /** @var Plan $plan */
        foreach ($result as $plan) {
            $plans[] = new PlanResult(
                id: $plan->getId()->toString(),
                tier: $plan->getTier()->value,
                gymsLimit: $plan->getGymsLimit(),
                staffLimit: $plan->getStaffLimit(),
                prices: array_map(fn (PlanPrice $planPrice) => new PlanPriceResult(
                    id: $planPrice->getId()->toString(),
                    amount: $planPrice->getPrice()->getValue(),
                    currency: $planPrice->getPrice()->getCurrency()->value,
                    interval: $planPrice->getInterval()->getUnit()->value,
                ), $plan->getPrices()->toArray()),
            );
        }

        return new PlanCollection($plans);
    }
}
