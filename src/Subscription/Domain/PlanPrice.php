<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;

final readonly class PlanPrice
{
    private function __construct(
        public Id $id,
        public string $stripeId,
        public int $intervalValue,
        public IntervalUnit $intervalUnit,
        public int $price,
        public Currency $currency,
        public PlanTier $tier,
        public int $gymsLimit,
        public int $staffLimit,
    ) {}

    public static function restore(
        Id $id,
        string $stripeId,
        int $intervalValue,
        IntervalUnit $intervalUnit,
        int $price,
        Currency $currency,
        PlanTier $tier,
        int $gymsLimit,
        int $staffLimit,
    ): self {
        return new self($id, $stripeId, $intervalValue, $intervalUnit, $price, $currency, $tier, $gymsLimit, $staffLimit);
    }
}
