<?php

declare(strict_types=1);

namespace App\Subscription\Application\Dto;

final readonly class WebhookEvent
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public string $type,
        public array $data,
    ) {}
}
