<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;

interface SubscriptionRepository
{
    public function findByOwnerId(Id $ownerId): ?Subscription;
}
