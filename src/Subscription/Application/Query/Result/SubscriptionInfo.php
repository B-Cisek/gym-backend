<?php

declare(strict_types=1);

namespace App\Subscription\Application\Query\Result;

readonly class SubscriptionInfo
{
    public function __construct(
        public ?string $id = null,
        public ?string $status = null,
        public ?string $startTime = null,
        public ?string $endTime = null,
        public ?string $cancelTime = null,
        public ?string $tier = null
    ) {}
}
