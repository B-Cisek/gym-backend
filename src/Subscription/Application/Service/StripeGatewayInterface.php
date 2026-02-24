<?php

declare(strict_types=1);

namespace App\Subscription\Application\Service;

use App\Subscription\Application\Dto\StripeSubscriptionData;

interface StripeGatewayInterface
{
    public function createCustomer(string $email, string $ownerId): string;

    public function createCheckoutSession(string $stripeCustomerId, string $stripePriceId, string $ownerId): string;

    public function getSubscription(string $stripeSubscriptionId): StripeSubscriptionData;

    public function createPortalSession(string $stripeCustomerId): string;

    /**
     * @return array<string, mixed>
     */
    public function getPrices(): array;
}
