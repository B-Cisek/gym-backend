<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

enum SubscriptionStatus: string
{
    case TRIALING = 'trialing';

    case ACTIVE = 'active';

    case INCOMPLETE = 'incomplete';

    case INCOMPLETE_EXPIRED = 'incomplete_expired';

    case PAST_DUE = 'past_due';

    case CANCELED = 'canceled';

    case UNPAID = 'unpaid';

    case PAUSED = 'paused';

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

    public function isUnpaid(): bool
    {
        return $this === self::UNPAID;
    }

    public function isPaused(): bool
    {
        return $this === self::PAUSED;
    }

    public function isPastDue(): bool
    {
        return $this === self::PAST_DUE;
    }

    public function isIncompleteExpired(): bool
    {
        return $this === self::INCOMPLETE_EXPIRED;
    }
}
