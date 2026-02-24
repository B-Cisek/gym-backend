<?php

declare(strict_types=1);

namespace App\Subscription\Application\Query;

use App\Subscription\Application\Query\Result\PlanCollection;

interface GetPlans
{
    public function execute(): PlanCollection;
}
