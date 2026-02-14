<?php

declare(strict_types=1);

namespace App\Gym\Application\Query;

use App\Gym\Application\Query\Result\GymCollection;

interface GetGyms
{
    public function execute(string $ownerId): GymCollection;
}
