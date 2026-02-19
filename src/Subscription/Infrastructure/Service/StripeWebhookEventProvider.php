<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Service;

use App\Subscription\Application\Service\StripeWebhookEventHandlerInterface;
use App\Subscription\Application\Service\StripeWebhookEventProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class StripeWebhookEventProvider implements StripeWebhookEventProviderInterface
{
    /**
     * @param iterable<StripeWebhookEventHandlerInterface> $handlers
     */
    public function __construct(
        #[AutowireIterator('stripe.webhook_event_handler')]
        private iterable $handlers,
    ) {}

    public function get(string $eventType): ?StripeWebhookEventHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($eventType)) {
                return $handler;
            }
        }

        return null;
    }
}
