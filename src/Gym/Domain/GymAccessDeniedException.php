<?php

declare(strict_types=1);

namespace App\Gym\Domain;

use App\Shared\Domain\DomainException;

final class GymAccessDeniedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Access denied to this gym');
    }

    public function getHttpStatusCode(): int
    {
        return 403;
    }
}
