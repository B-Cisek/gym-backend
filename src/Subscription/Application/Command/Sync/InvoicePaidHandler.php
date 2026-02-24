<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Shared\Application\Command\Sync\CommandHandler;
use App\Subscription\Domain\SubscriptionRepository;
use App\Subscription\Domain\SubscriptionStatus;
use Psr\Log\LoggerInterface;

final readonly class InvoicePaidHandler implements CommandHandler
{
    public function __construct(
        private LoggerInterface $stripeLogger,
        private SubscriptionRepository $subscriptionRepository,
    ) {}

    public function __invoke(InvoicePaid $command): void
    {
        $this->stripeLogger->info('STRIPE_WEBHOOK: INVOICE_PAID', [
            'data' => $command->event->data,
        ]);

        $data = $command->event->data['object'];
        $stripeSubscriptionId = $data['subscription'] ?? '';

        $subscription = $this->subscriptionRepository->findByStripeSubscriptionId($stripeSubscriptionId);

        if ($subscription === null) {
            $this->stripeLogger->warning('STRIPE_WEBHOOK: subscription not found', [
                'stripeSubscriptionId' => $stripeSubscriptionId,
            ]);

            return;
        }

        $updated = $subscription->update(
            status: SubscriptionStatus::ACTIVE,
            endTime: \DateTimeImmutable::createFromTimestamp($data['period_end'])
                ->setTimezone(new \DateTimeZone('Europe/Warsaw')),
            cancelTime: $subscription->cancelTime,
        );

        $this->subscriptionRepository->update($updated);
    }
}
