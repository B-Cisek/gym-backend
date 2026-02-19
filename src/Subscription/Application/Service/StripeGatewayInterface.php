<?php

declare(strict_types=1);

namespace App\Subscription\Application\Service;

use Stripe\Collection;

interface StripeGatewayInterface
{
    public function createCustomer(string $email, string $ownerId): string;

    public function createCheckoutSession(string $stripeCustomerId, string $stripePriceId): string;

    public function getPrices(): Collection;
}
