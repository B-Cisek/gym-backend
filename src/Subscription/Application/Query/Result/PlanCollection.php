<?php

declare(strict_types=1);

namespace App\Subscription\Application\Query\Result;

final readonly class PlanCollection
{
    /** @param PlanResult[] $plans */
    public function __construct(public array $plans) {}
}
