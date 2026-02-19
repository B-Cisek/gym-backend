<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Shared\Application\Command\Sync\CommandHandler;
use App\Subscription\Application\Service\StripeWebhookEventProviderInterface;
use App\Subscription\Application\Service\WebhookSignatureVerifierInterface;
use Psr\Log\LoggerInterface;
use Stripe\Exception\SignatureVerificationException;

final readonly class HandleStripeWebhookHandler implements CommandHandler
{
    public function __construct(
        private WebhookSignatureVerifierInterface $signatureVerifier,
        private StripeWebhookEventProviderInterface $provider,
        private LoggerInterface $logger
    ) {}

    public function __invoke(HandleStripeWebhook $command): void
    {
        try {
            $event = $this->signatureVerifier->verify($command->payload, $command->signatureHeader);

            $eventHandler = $this->provider->get($event->type);

            $eventHandler?->process($event);
        } catch (SignatureVerificationException $e) {
            $this->logger->error('STRIPE_SIGNATURE_VERIFICATION_ERROR', [
                'exception' => $e,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('STRIPE_WEBHOOK_ERROR', [
                'exception' => $e,
            ]);
        }
    }
}
