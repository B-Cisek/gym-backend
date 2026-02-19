<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;

class PlanPrice
{
    private function __construct(
        public Id $id,
        public int $intervalValue,
        public IntervalUnit $intervalUnit,
        public int $price,
        public Currency $currency,
        public PlanTier $tier,
        public int $gymsLimit,
        public int $staffLimit,
    )
    {
    }
}
