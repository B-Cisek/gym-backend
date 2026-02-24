<?php

declare(strict_types=1);

namespace App\Subscription\Application\Webhook;

use App\Shared\Application\Command\Sync\CommandBus;
use App\Subscription\Application\Command\Sync\SubscriptionDeleted as SubscriptionDeletedMessage;
use App\Subscription\Application\Dto\WebhookEvent;
use App\Subscription\Application\Service\StripeWebhookEventHandlerInterface;

final readonly class SubscriptionDeleted implements StripeWebhookEventHandlerInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function supports(string $eventType): bool
    {
        return $eventType === 'customer.subscription.deleted';
    }

    public function process(WebhookEvent $event): void
    {
        $this->commandBus->dispatch(new SubscriptionDeletedMessage($event));
    }
}
