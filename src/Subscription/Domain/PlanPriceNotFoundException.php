<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\DomainException;

final class PlanPriceNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Plan price not found');
    }

    public function getHttpStatusCode(): int
    {
        return 404;
    }
}
