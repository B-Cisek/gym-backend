<?php

declare(strict_types=1);

namespace App\Owner\Application\Query;

use App\Owner\Application\Query\Result\OwnerSettings;

interface GetOwnerSettings
{
    public function execute(string $ownerId): OwnerSettings;
}
