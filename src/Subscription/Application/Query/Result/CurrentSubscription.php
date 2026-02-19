<?php

declare(strict_types=1);

namespace App\Subscription\Application\Query\Result;

final readonly class CurrentSubscription
{
    public function __construct(
        public string $id,
        public string $status,
        public string $planTier,
        public int $priceAmount,
        public string $priceCurrency,
        public string $intervalUnit,
        public int $intervalValue,
        public string $startTime,
        public ?string $endTime,
        public ?string $cancelTime,
    ) {}
}
