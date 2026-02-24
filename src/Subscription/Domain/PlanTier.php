<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

enum PlanTier: string
{
    case BASIC = 'basic';
    case PRO = 'pro';
    case UNLIMITED = 'unlimited';
    public const string ROLE_OWNER_BASIC = 'ROLE_OWNER_BASIC';
    public const string ROLE_OWNER_PRO = 'ROLE_OWNER_PRO';
    public const string ROLE_OWNER_UNLIMITED = 'ROLE_OWNER_UNLIMITED';

    public function getRole(): string
    {
        return match ($this) {
            self::BASIC => self::ROLE_OWNER_BASIC,
            self::PRO => self::ROLE_OWNER_PRO,
            self::UNLIMITED => self::ROLE_OWNER_UNLIMITED,
        };
    }
}
