<?php

declare(strict_types=1);

namespace App\Subscription\Application\Service;

interface StripeWebhookEventHandlerProviderInterface
{
    public function get(string $eventType): ?StripeWebhookEventHandlerInterface;
}
