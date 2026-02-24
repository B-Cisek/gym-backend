<?php

declare(strict_types=1);

namespace App\Subscription\Application\Query;

use App\Subscription\Application\Query\Result\SubscriptionInfo;

interface GetSubscriptionInfo
{
    public function execute(string $ownerId): SubscriptionInfo;
}
