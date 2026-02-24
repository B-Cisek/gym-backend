<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Stripe;

use App\Subscription\Application\Dto\StripeSubscriptionData;
use App\Subscription\Application\Service\StripeGatewayInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

final readonly class StripeGateway implements StripeGatewayInterface
{
    public function __construct(
        private StripeClient $stripeClient,
        private string $checkoutSuccessUrl,
        private string $checkoutCancelUrl,
        private string $portalReturnUrl,
    ) {}

    /**
     * @throws ApiErrorException
     */
    public function createCustomer(string $email, string $ownerId): string
    {
        $customer = $this->stripeClient->customers->create([
            'email' => $email,
            'metadata' => ['owner_id' => $ownerId],
        ]);

        return $customer->id;
    }

    public function createCheckoutSession(string $stripeCustomerId, string $stripePriceId, string $ownerId): string
    {
        $session = $this->stripeClient->checkout->sessions->create([
            'customer' => $stripeCustomerId,
            'mode' => 'subscription',
            'line_items' => [
                [
                    'price' => $stripePriceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => $this->checkoutSuccessUrl,
            'cancel_url' => $this->checkoutCancelUrl,
            'metadata' => ['owner_id' => $ownerId],
        ]);

        return $session->url;
    }

    public function getSubscription(string $stripeSubscriptionId): StripeSubscriptionData
    {
        $sub = $this->stripeClient->subscriptions->retrieve($stripeSubscriptionId, ['expand' => ['items']]);

        $data = $sub->toArray();

        return new StripeSubscriptionData(
            stripeId: $data['id'],
            stripePriceId: $data['items']['data'][0]['price']['id'],
            status: $data['status'],
            startTime: \DateTimeImmutable::createFromTimestamp($data['items']['data'][0]['current_period_start'])
                ->setTimezone(new \DateTimeZone('Europe/Warsaw')),
            endTime: \DateTimeImmutable::createFromTimestamp($data['items']['data'][0]['current_period_end'])
                ->setTimezone(new \DateTimeZone('Europe/Warsaw')),
        );
    }

    public function createPortalSession(string $stripeCustomerId): string
    {
        $session = $this->stripeClient->billingPortal->sessions->create([
            'customer' => $stripeCustomerId,
            'return_url' => $this->portalReturnUrl,
        ]);

        return $session->url;
    }

    /** @return array<mixed, mixed> */
    public function getPrices(): array
    {
        return $this->stripeClient->prices->all()->toArray();
    }
}
