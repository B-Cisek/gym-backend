<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Stripe;

use App\Subscription\Application\Dto\WebhookEvent;
use App\Subscription\Application\Service\WebhookSignatureVerifierInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class WebhookSignatureVerifier implements WebhookSignatureVerifierInterface
{
    public function __construct(
        #[Autowire(param: 'stripe.webhook_secret')]
        private string $webhookSecret,
    ) {}

    /**
     * @throws SignatureVerificationException
     */
    public function verify(string $payload, string $signatureHeader): WebhookEvent
    {
        $event = Webhook::constructEvent($payload, $signatureHeader, $this->webhookSecret);

        return new WebhookEvent(
            type: $event->type,
            data: $event->data->toArray(),
        );
    }
}
