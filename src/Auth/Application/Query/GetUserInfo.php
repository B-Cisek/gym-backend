<?php

declare(strict_types=1);

namespace App\Auth\Application\Query;

use App\Auth\Application\Query\Result\MeResponse;

interface GetUserInfo
{
    public function execute(string $userId): MeResponse;
}
