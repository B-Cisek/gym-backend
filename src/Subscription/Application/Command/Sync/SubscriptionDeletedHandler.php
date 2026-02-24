<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Shared\Application\Command\Sync\CommandHandler;
use App\Subscription\Domain\SubscriptionRepository;
use App\Subscription\Domain\SubscriptionStatus;
use Psr\Log\LoggerInterface;

final readonly class SubscriptionDeletedHandler implements CommandHandler
{
    public function __construct(
        private LoggerInterface $stripeLogger,
        private SubscriptionRepository $subscriptionRepository,
    ) {}

    public function __invoke(SubscriptionDeleted $command): void
    {
        $this->stripeLogger->info('STRIPE_WEBHOOK: CUSTOMER_SUBSCRIPTION_DELETED', [
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

        $canceledAt = $data['canceled_at'] !== null
            ? \DateTimeImmutable::createFromTimestamp($data['canceled_at'])
                ->setTimezone(new \DateTimeZone('Europe/Warsaw'))
            : new \DateTimeImmutable();

        $updated = $subscription->update(
            status: SubscriptionStatus::CANCELED,
            endTime: $subscription->endTime,
            cancelTime: $canceledAt,
        );

        $this->subscriptionRepository->update($updated);
    }
}
