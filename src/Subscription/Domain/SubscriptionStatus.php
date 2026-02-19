<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

enum SubscriptionStatus: string
{
    case INCOMPLETE = 'incomplete';

    case TRIALING = 'trialing';

    case ACTIVE = 'active';

    case CANCELED = 'canceled';

    public function isIncomplete(): bool
    {
        return $this === self::INCOMPLETE;
    }

    public function isTrialing(): bool
    {
        return $this === self::TRIALING;
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }
}
