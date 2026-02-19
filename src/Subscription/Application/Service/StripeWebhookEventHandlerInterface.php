<?php

declare(strict_types=1);

namespace App\Subscription\Application\Service;

use App\Subscription\Application\Dto\WebhookEvent;

interface StripeWebhookEventHandlerInterface
{
    public function supports(string $eventType): bool;

    public function process(WebhookEvent $event): void;
}
