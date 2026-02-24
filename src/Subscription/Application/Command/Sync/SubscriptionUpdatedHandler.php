<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Shared\Application\Command\Sync\CommandHandler;
use App\Subscription\Domain\SubscriptionRepository;
use App\Subscription\Domain\SubscriptionStatus;
use Psr\Log\LoggerInterface;

final readonly class SubscriptionUpdatedHandler implements CommandHandler
{
    public function __construct(
        private LoggerInterface $stripeLogger,
        private SubscriptionRepository $subscriptionRepository,
    ) {}

    public function __invoke(SubscriptionUpdated $command): void
    {
        $this->stripeLogger->info('STRIPE_WEBHOOK: CUSTOMER_SUBSCRIPTION_UPDATED', [
            'data' => $command->event->data,
        ]);

        $data = $command->event->data['object'];
        $stripeSubscriptionId = $data['id'];

        $subscription = $this->subscriptionRepository->findByStripeSubscriptionId($stripeSubscriptionId);

        if ($subscription === null) {
            $this->stripeLogger->warning('STRIPE_WEBHOOK: subscription not found', [
                'stripeSubscriptionId' => $stripeSubscriptionId,
            ]);

            return;
        }

        $canceledAt = $subscription->cancelTime;

        if ($canceledAt === null && $data['cancel_at'] !== null) {
            $canceledAt = new \DateTimeImmutable();
        }

        $updated = $subscription->update(
            status: SubscriptionStatus::from($data['status']),
            endTime: \DateTimeImmutable::createFromTimestamp($data['current_period_end'])
                ->setTimezone(new \DateTimeZone('Europe/Warsaw')),
            cancelTime: $canceledAt,
        );

        $this->subscriptionRepository->update($updated);
    }
}
