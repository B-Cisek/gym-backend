<?php

declare(strict_types=1);

namespace App\Subscription\Application\Dto;

final readonly class StripeSubscriptionData
{
    public function __construct(
        public string $stripeId,
        public string $stripePriceId,
        public string $status,
        public \DateTimeImmutable $startTime,
        public \DateTimeImmutable $endTime,
    ) {}
}
