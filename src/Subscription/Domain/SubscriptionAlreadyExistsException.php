<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\DomainException;

final class SubscriptionAlreadyExistsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Owner already has an active subscription');
    }

    public function getHttpStatusCode(): int
    {
        return 409;
    }
}
