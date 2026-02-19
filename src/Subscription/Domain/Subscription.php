<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;
use DateTimeImmutable;

final readonly class Subscription
{
    private function __construct(
        public Id $id,
        public Id $ownerId,
        public SubscriptionStatus $status,
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
        public ?DateTimeImmutable $cancelTime,
    ) {}

    public static function restore(
        Id $id,
        Id $ownerId,
        SubscriptionStatus $status,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        ?DateTimeImmutable $cancelTime,
    ): self {
        return new self($id, $ownerId, $status, $startTime, $endTime, $cancelTime);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
