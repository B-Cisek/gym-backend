<?php

declare(strict_types=1);

namespace App\Subscription\Application\Webhook;

use App\Shared\Application\Command\Sync\CommandBus;
use App\Subscription\Application\Command\Sync\InvoicePaid as InvoicePaidMessage;
use App\Subscription\Application\Dto\WebhookEvent;
use App\Subscription\Application\Service\StripeWebhookEventHandlerInterface;

final readonly class InvoicePaid implements StripeWebhookEventHandlerInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function supports(string $eventType): bool
    {
        return $eventType === 'invoice.paid';
    }

    public function process(WebhookEvent $event): void
    {
        $this->commandBus->dispatch(new InvoicePaidMessage($event));
    }
}
