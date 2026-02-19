<?php

declare(strict_types=1);

namespace App\Subscription\Application\Query;

use App\Subscription\Application\Query\Result\CurrentSubscription;

interface GetCurrentSubscription
{
    public function execute(string $ownerId): ?CurrentSubscription;
}
