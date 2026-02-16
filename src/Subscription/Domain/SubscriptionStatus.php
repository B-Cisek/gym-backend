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
        return self::INCOMPLETE === $this;
    }

    public function isTrialing(): bool
    {
        return self::TRIALING === $this;
    }

    public function isActive(): bool
    {
        return self::ACTIVE === $this;
    }

    public function isCanceled(): bool
    {
        return self::CANCELED === $this;
    }
}

