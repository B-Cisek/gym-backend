<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Http\Controller\V1;

use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use App\Subscription\Application\Command\Sync\HandleStripeWebhook;
use Psr\Log\LoggerInterface;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final readonly class StripeWebhookController
{
    public function __construct(
        private CommandBus $commandBus,
        private JsonResponseFactory $jsonResponseFactory,
        private LoggerInterface $stripeLogger,
    ) {}

    #[Route(path: '/stripe/webhook', name: 'stripe.webhook', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(new HandleStripeWebhook(
                payload: $request->getContent(),
                signatureHeader: $request->headers->get('Stripe-Signature', ''),
            ));
        } catch (SignatureVerificationException $e) {
            $this->stripeLogger->error('STRIPE_SIGNATURE_VERIFICATION_ERROR', [
                'exception' => $e,
            ]);

            $this->jsonResponseFactory->error('Stripe signature verification failed.');
        } catch (\Throwable $e) {
            $this->stripeLogger->error('STRIPE_WEBHOOK_ERROR', [
                'exception' => $e,
            ]);

            $this->jsonResponseFactory->error('Error occurred while processing Stripe webhook.');
        }

        return $this->jsonResponseFactory->success();
    }
}
