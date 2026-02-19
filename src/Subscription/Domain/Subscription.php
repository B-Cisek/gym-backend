<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;
use DateTimeImmutable;

class Subscription
{
    private function __construct(
        public Id $id,
        public Id $ownerId,
        public SubscriptionStatus $status,
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
        public DateTimeImmutable $cancelTime
    )
    {

    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
