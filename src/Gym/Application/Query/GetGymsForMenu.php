<?php

declare(strict_types=1);

namespace App\Gym\Application\Query;

use App\Gym\Application\Query\Result\GymMenuCollection;

interface GetGymsForMenu
{
    public function execute(string $ownerId): GymMenuCollection;
}
