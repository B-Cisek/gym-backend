<?php

declare(strict_types=1);

namespace App\Subscription\Application\Query\Result;

final readonly class PlanResult
{
    /** @param PlanPriceResult[] $prices */
    public function __construct(
        public string $id,
        public string $tier,
        public int $gymsLimit,
        public int $staffLimit,
        public array $prices,
    ) {}
}
