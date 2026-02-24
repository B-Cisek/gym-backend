<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\DomainException;

final class NoStripeCustomerException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Owner does not have a Stripe customer account');
    }

    public function getHttpStatusCode(): int
    {
        return 400;
    }
}
