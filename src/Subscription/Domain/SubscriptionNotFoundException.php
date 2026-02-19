<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\DomainException;

final class SubscriptionNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Subscription not found');
    }

    public function getHttpStatusCode(): int
    {
        return 404;
    }
}
