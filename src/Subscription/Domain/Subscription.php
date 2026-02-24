<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;

final readonly class Subscription
{
    private function __construct(
        public Id $id,
        public Id $ownerId,
        public Id $planPriceId,
        public SubscriptionStatus $status,
        public \DateTimeImmutable $startTime,
        public \DateTimeImmutable $endTime,
        public string $stripeSubscriptionId,
        public ?\DateTimeImmutable $cancelTime,
    ) {}

    public static function create(
        Id $id,
        Id $ownerId,
        Id $planPriceId,
        SubscriptionStatus $status,
        \DateTimeImmutable $startTime,
        \DateTimeImmutable $endTime,
        string $stripeSubscriptionId,
        ?\DateTimeImmutable $cancelTime = null,
    ): self {
        return new self($id, $ownerId, $planPriceId, $status, $startTime, $endTime, $stripeSubscriptionId, $cancelTime);
    }

    public static function restore(
        Id $id,
        Id $ownerId,
        Id $planPriceId,
        SubscriptionStatus $status,
        \DateTimeImmutable $startTime,
        \DateTimeImmutable $endTime,
        string $stripeSubscriptionId,
        ?\DateTimeImmutable $cancelTime,
    ): self {
        return new self($id, $ownerId, $planPriceId, $status, $startTime, $endTime, $stripeSubscriptionId, $cancelTime);
    }

    public function update(
        SubscriptionStatus $status,
        \DateTimeImmutable $endTime,
        ?\DateTimeImmutable $cancelTime,
    ): self {
        return new self(
            $this->id,
            $this->ownerId,
            $this->planPriceId,
            $status,
            $this->startTime,
            $endTime,
            $this->stripeSubscriptionId,
            $cancelTime,
        );
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getStripeSubscriptionId(): string
    {
        return $this->stripeSubscriptionId;
    }
}
