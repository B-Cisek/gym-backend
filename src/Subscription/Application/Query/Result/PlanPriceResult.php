<?php

declare(strict_types=1);

namespace App\Subscription\Application\Query\Result;

final readonly class PlanPriceResult
{
    public function __construct(
        public string $id,
        public int $amount,
        public string $currency,
        public string $interval,
    ) {}
}
