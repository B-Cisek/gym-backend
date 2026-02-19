<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;

interface PlanPriceRepository
{
    public function get(Id $id): PlanPrice;

    public function findByStripeId(string $stripeId): ?PlanPrice;
}
