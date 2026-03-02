<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Owner\Domain\OwnerRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Application\Service\IdGeneratorInterface;
use App\Shared\Domain\Id;
use App\Subscription\Application\Service\StripeGatewayInterface;
use App\Subscription\Domain\PlanPriceNotFoundException;
use App\Subscription\Domain\PlanPriceRepository;
use App\Subscription\Domain\Subscription;
use App\Subscription\Domain\SubscriptionRepository;
use App\Subscription\Domain\SubscriptionStatus;
use Psr\Log\LoggerInterface;

final readonly class CheckoutSessionCompletedHandler implements CommandHandler
{
    public function __construct(
        private LoggerInterface $stripeLogger,
        private OwnerRepository $ownerRepository,
        private SubscriptionRepository $subscriptionRepository,
        private PlanPriceRepository $planPriceRepository,
        private StripeGatewayInterface $stripeGateway,
        private IdGeneratorInterface $idGenerator,
    ) {}

    public function __invoke(CheckoutSessionCompleted $command): void
    {
        $this->stripeLogger->info('STRIPE_WEBHOOK: CHECKOUT_SESSION_COMPLETED', [
            'data' => $command->event->data,
        ]);

        $data = $command->event->data['object'];
        $ownerId = new Id($data['metadata']['owner_id']);
        $stripeSubscriptionId = $data['subscription'];

        $existing = $this->subscriptionRepository->findByOwnerId($ownerId);

        if ($existing !== null && $existing->stripeSubscriptionId === $stripeSubscriptionId) {
            return;
        }

        $stripeSubscription = $this->stripeGateway->getSubscription($stripeSubscriptionId);
        $planPrice = $this->planPriceRepository->findByStripeId($stripeSubscription->stripePriceId);

        if ($planPrice === null) {
            throw new PlanPriceNotFoundException();
        }

        $owner = $this->ownerRepository->get($ownerId);

        $subscription = Subscription::create(
            id: $this->idGenerator->generate(),
            owner: $owner,
            planPrice: $planPrice,
            status: SubscriptionStatus::from($stripeSubscription->status),
            startTime: $stripeSubscription->startTime,
            endTime: $stripeSubscription->endTime,
            stripeSubscriptionId: $stripeSubscriptionId,
        );

        $this->subscriptionRepository->save($subscription);
    }
}
