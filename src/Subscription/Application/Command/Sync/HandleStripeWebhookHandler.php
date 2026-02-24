<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Shared\Application\Command\Sync\CommandHandler;
use App\Subscription\Application\Service\StripeWebhookEventHandlerProviderInterface;
use App\Subscription\Application\Service\WebhookSignatureVerifierInterface;

final readonly class HandleStripeWebhookHandler implements CommandHandler
{
    public function __construct(
        private WebhookSignatureVerifierInterface $signatureVerifier,
        private StripeWebhookEventHandlerProviderInterface $provider,
    ) {}

    public function __invoke(HandleStripeWebhook $command): void
    {
        $event = $this->signatureVerifier->verify($command->payload, $command->signatureHeader);

        $webhookHandler = $this->provider->get($event->type);

        $webhookHandler?->process($event);
    }
}
