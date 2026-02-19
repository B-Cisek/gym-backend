<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Repository;

use App\Subscription\Domain\PlanRepository as DomainPlanRepository;
use App\Subscription\Infrastructure\Doctrine\Repository\PlanRepository as DoctrinePlanRepository;

readonly class PlanRepository implements DomainPlanRepository
{
    public function __construct(private DoctrinePlanRepository $doctrineRepository)
    {
    }


}
